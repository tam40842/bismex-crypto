<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use DB;
use Gate;

class TransactionsController extends Controller
{
    public function index()
    {
        Gate::allows('modules', 'tracking_balance_access');

        $data = [];
        $transactions =  DB::table('transactions as t')
            ->leftjoin('users as u', 't.userid', '=', 'u.id')
            ->orderBy('t.created_at','desc')
            ->select('t.*', 'u.username as username')
            ->paginate(10);

        $data['transactions'] = $transactions;
        return view('admin.transactions.index', $data);
    }

    public function getFilters(Request $request)
    {
        if ($request->has('paginate')) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $transactions =  DB::table('transactions as t')
            ->leftjoin('users as u', 't.userid', '=', 'u.id')
            ->when($request->get('start_day') && $request->get('end_day'), function ($query) use ($request) {
                $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
                if (date($request->end_day) == date('Y-m-d')) {
                    $end_day = date('Y-m-d H:i:s');
                } else {
                    $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
                }
                return $query->whereBetween('t.created_at', [$start_day, $end_day]);
            })
            ->when($request->get('start_day') && !$request->get('end_day'), function ($query) use ($request) {
                $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
                return $query->where('t.created_at', '>', $start_day);
            })
            ->when(!$request->get('start_day') && $request->get('end_day'), function ($query) use ($request) {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
                return $query->where('t.created_at', '<', $end_day);
            })
            ->when($request->get('wallet_type'), function ($query) use ($request) {
                return $query->where('t.wallet_type', '=', $request->get('wallet_type'));
            })
            ->when($request->get('keyword'), function ($query) use ($request) {
                return $query->where(function ($query1) use ($request) {
                    $query1->orWhere('u.username', 'like', '%' . $request->get('keyword') . '%')
                    ->orWhere('t.message', 'like', '%' . $request->get('keyword') . '%')
                        ->orWhere('t.type', 'like', '%' . $request->get('keyword') . '%');
                });
            })
            ->select('t.*', 'u.username as username')
            ->orderBy('t.created_at','desc')
            ->paginate($paginate);

        $data['filter'] = [
            'start_day' => $request->start_day,
            'end_day' => $request->end_day,
            'wallet_type' => $request->wallet_type,
            'keyword' => $request->keyword,
            'paginate' => $paginate
        ];

        $transactions->appends($data['filter']);
        $data['transactions'] = $transactions;
        return view('admin.transactions.index', $data);
    }
}
