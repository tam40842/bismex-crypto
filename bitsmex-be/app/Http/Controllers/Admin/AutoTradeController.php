<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoTradeController extends Controller
{
    public function index()
    {
        $data = [];
        $packages =  DB::table('autotrade_package as p')
            ->leftjoin('users as u', 'p.userid', '=', 'u.id')
            ->orderBy('p.created_at', 'desc')
            ->select('p.*', 'u.username as username')
            ->paginate(10);

        $data['packages'] = $packages;
        return view('admin.autotrade.index', $data);
    }

    public function getFilters(Request $request)
    {
        if ($request->has('paginate')) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $packages =  DB::table('autotrade_package as p')
            ->leftjoin('users as u', 'p.userid', '=', 'u.id')
            ->when($request->get('start_day') && $request->get('end_day'), function ($query) use ($request) {
                $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
                if (date($request->end_day) == date('Y-m-d')) {
                    $end_day = date('Y-m-d H:i:s');
                } else {
                    $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
                }
                return $query->whereBetween('p.created_at', [$start_day, $end_day]);
            })
            ->when($request->get('start_day') && !$request->get('end_day'), function ($query) use ($request) {
                $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
                return $query->where('p.created_at', '>', $start_day);
            })
            ->when(!$request->get('start_day') && $request->get('end_day'), function ($query) use ($request) {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
                return $query->where('p.created_at', '<', $end_day);
            })
            ->when($request->get('status') != null, function ($query) use ($request) {
                return $query->where('p.status', '=', $request->get('status'));
            })
            ->when($request->get('keyword'), function ($query) use ($request) {
                return $query->where(function ($query1) use ($request) {
                    $query1->orWhere('p.package_id', 'like', '%' . $request->get('keyword') . '%')
                        ->orWhere('u.username', 'like', '%' . $request->get('keyword') . '%');
                });
            })
            ->select('p.*', 'u.username as username')
            ->orderBy('p.created_at', 'desc')
            ->paginate($paginate);

        $data['filter'] = [
            'start_day' => $request->start_day,
            'end_day' => $request->end_day,
            'status' => $request->status,
            'keyword' => $request->keyword,
            'paginate' => $paginate
        ];

        $packages->appends($data['filter']);
        $data['packages'] = $packages;
        return view('admin.autotrade.index', $data);
    }

    public function getEdit(Request $request, $id)
    {
        $data['package'] = DB::table('autotrade_package as p')
            ->leftjoin('users as u', 'p.userid', '=', 'u.id')
            ->where('p.package_id', $id)
            ->orderBy('p.created_at', 'desc')
            ->select('p.*', 'u.username as username')->first();
        if (is_null($data['package'])) {
            return redirect()->route('admin.autotrade')->with('alert_error', 'Autotrade does not exist.');
        }

        return view('admin.autotrade.edit', $data);
    }

    public function postWithdrawCancel($package_id)
    {
        $package = DB::table('autotrade_package as p')->where('p.withdraw_status', 1)->where('p.status', 1)->where('p.package_id', $package_id)->first();
        if (is_null($package)) {
            return redirect()->route('admin.autotrade')->with('alert_error', 'Autotrade does not exist.');
        }

        DB::beginTransaction();
        try {
            DB::table('autotrade_package as p')
                ->where('p.withdraw_status', 1)
                ->where('p.status', 1)
                ->where('p.package_id', $package_id)
                ->update([
                    'withdraw_amount' => 0,
                    'withdraw_date' => null,
                    'withdraw_status' => 3
                ]);
            DB::commit();
            return redirect()->route('admin.autotrade')->with('alert_success', 'Đã hủy yêu cầu rút.');
        } catch (QueryException $ex) {
            DB::rollBack();
            return redirect()->route('admin.autotrade')->with('alert_error', 'Server has a Error.');
        }
    }

    public function postWithdrawApproval($package_id)
    {
        $package = DB::table('autotrade_package as p')->where('p.withdraw_status', 1)->where('p.status', 1)->where('p.package_id', $package_id)->first();
        if (is_null($package)) {
            return redirect()->route('admin.autotrade')->with('alert_error', 'Autotrade does not exist.');
        }

        DB::beginTransaction();
        try {
            DB::table('autotrade_package as p')
                ->where('p.withdraw_status', 1)
                ->where('p.status', 1)
                ->where('p.package_id', $package_id)->increment('withdraw_complete',$package->withdraw_amount);
            DB::table('autotrade_package as p')
                ->where('p.withdraw_status', 1)
                ->where('p.status', 1)
                ->where('p.package_id', $package_id)
                ->update([
                    'withdraw_amount' => 0,
                    'withdraw_date' => null,
                    'withdraw_status' => 2
                ]);

            TransactionHistory::historyLiveBalance($package->userid, 'AUTOTRADE_WITHDRAW', $package->withdraw_amount, 'autotrade_balance');
            DB::table('users')->where('id', $package->userid)->where('status', 1)->increment('autotrade_balance', $package->withdraw_amount);
            DB::commit();
            return redirect()->route('admin.autotrade')->with('alert_success', 'Đã hủy yêu cầu rút.');
        } catch (QueryException $ex) {
            DB::rollBack();
            return redirect()->route('admin.autotrade')->with('alert_error', 'Server has a Error.');
        }
    }
}
