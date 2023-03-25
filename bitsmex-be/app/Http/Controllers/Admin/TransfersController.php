<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\TransactionHistory;
use App\Twofa;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\AntiCheat\Entities\Anti;

class TransfersController extends Controller
{
    public function index()
    {
        Gate::allows('modules', 'finance_transfers_access');

        $data = [
            'transfers' => DB::table('transfers')
                ->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'r.id')
                ->orderBy('id', 'desc')->select('transfers.*', 's.username as sender', 's.admin_setup', 'r.username as receiver')->paginate(100),
            'transfers_status' => $this->transfers_status(),
            'status_admin_setup' => $this->user_admin_setup_status(),
        ];
        return view('admin.transfers.index', $data);
    }

    public function getEdit(Request $request, $transfers_id)
    {
        Gate::allows('modules', 'finance_transfers_edit');

        $transfers = DB::table('transfers')->where('transfers.transfer_id', $transfers_id)
            ->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'r.id')
            // ->where('s.admin_setup', 0)
            ->select('transfers.*', 's.username as sender', 'r.username as receiver')->first();
        if (is_null($transfers)) {
            return redirect()->route('admin.transfers')->with('alert_error', 'The transferss does not exists.');
        }
        $data = [
            'transfers' => $transfers,
            'user' => DB::table('users')->where('id', $transfers->userid)->first(),
            'transfers_status' => $this->transfers_status(),
        ];
        return view('admin.transfers.edit', $data);
    }

    public function postSearch(Request $request)
    {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if (!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $transfers = DB::table('transfers')->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'r.id')->where(function ($query) use ($search_text) {
            $query->where('transfers.transfer_id', 'LIKE', '%' . $search_text . '%')
                ->orWhere('transfers.amount', 'LIKE', '%' . $search_text . '%')
                ->orWhere('r.username', 'LIKE', '%' . $search_text . '%')
                ->orWhere('s.username', 'LIKE', '%' . $search_text . '%')
                ->orWhere('r.email', 'LIKE', '%' . $search_text . '%')
                ->orWhere('s.email', 'LIKE', '%' . $search_text . '%')
                ->orWhere('r.phone_number', 'LIKE', '%' . $search_text . '%')
                ->orWhere('s.phone_number', 'LIKE', '%' . $search_text . '%');
        })->select('transfers.*', 's.username as sender', 's.admin_setup', 'r.username as receiver')->orderBY('transfers.id', 'desc')->paginate(100);

        $data = [
            'transfers' => $transfers,
            'transfers_status' => $this->transfers_status(),
            'status_admin_setup' => $this->user_admin_setup_status(),
        ];
        return view('admin.transfers.index', $data)->render();
    }

    public function getFilters(Request $request)
    {
        if ($request->has('start_day') && $request->has('end_day')) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            if (date($request->end_day) == date('Y-m-d')) {
                $end_day = date('Y-m-d H:i:s');
            } else {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            }
            $transfers = DB::table('transfers')->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'r.id')
                ->whereBetween('transfers.created_at', [$start_day, $end_day])
                ->where('s.admin_setup', $request->admin_setup)
                ->where('transfers.status', $request->status)
                ->select('transfers.*', 's.username as sender', 's.admin_setup', 'r.username as receiver')->orderBy('transfers.id', 'desc')->paginate($request->paginate);
            $data = [
                'transfers' => $transfers,
                'transfers_status' => $this->transfers_status(),
                'status_admin_setup' => $this->user_admin_setup_status(),
                'filter' => [
                    'start_day' => $request->start_day,
                    'end_day' => $request->end_day,
                    'status' => $request->status,
                    'paginate' => $request->paginate,
                    'admin_setup' => $request->admin_setup,
                ]
            ];
            return view('admin.transfers.index', $data);
        }
        return redirect()->route('admin.transfers');
    }

    public function getApproved(Request $request, $transfers_id)
    {
        Gate::allows('modules', 'finance_transfers_edit');

        $transfer = DB::table('transfers')->where('transfer_id', $transfers_id)->where('status', 0)->first();
        if (is_null($transfer)) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }

        $author = Auth::user();
        if (!$request->has('twofa_code')) {
            return redirect()->back()->with('alert_error', 'Vui lòng nhập mã Authy của bạn.');
        }

        if ($author->google2fa_enable) {
            $twofa = new Twofa();
            $passSecret = ($author->google2fa_secret);
            $valid = $twofa->verifyCode($passSecret, $request->twofa_code);
            if (!$valid) {
                return redirect()->back()->with('alert_error', 'Mã bảo mật của bạn không đúng. Xin vui lòng thử lại.');
            }
        }

        DB::beginTransaction();
        try {
            
            $user = DB::table('users')->where('id',$transfer->userid)->lockForUpdate()->first();
            if (is_null($user)) {
                return redirect()->back()->with('alert_error', 'Tài khoản người gửi không tồn tại hoặc bị khóa.');
            }

            $recipient = DB::table('users')->where('id', $transfer->recipient_id)->where('admin_setup', 0)->where('status', 1)->lockForUpdate()->first();
            if (is_null($recipient)) {
                return redirect()->back()->with('alert_error', 'Tài khoản người nhận không tồn tại.');
            }

            TransactionHistory::historyLiveBalance($recipient->id, 'TRANSFER', $transfer->total, 'primary_balance', 'TRANSFER ID: ' . $transfer->transfer_id);
            DB::table('users')->where('id', $recipient->id)->lockForUpdate()->increment('primary_balance', $transfer->total);
            Anti::firewall($user->id, 'transfer', [
                'user' => $user,
                'amount' => $transfer->amount,
                'recipient_id' => $recipient->id,
                'created_at' => date(now())
            ]);
            SendEmail::dispatch($user->email, 'You have just transferred money to another account', 'transfer', [
                'user' => $user,
                'amount' => $transfer->amount,
                'created_at' => $transfer->created_at,
                'receiver' => $recipient->username
            ]);
            SendEmail::dispatch($recipient->email, 'You have just received a money transfer', 'received', [
                'user' => $recipient,
                'amount' => $transfer->total,
                'created_at' => $transfer->created_at,
                'sender' => $user->username
            ]);

            DB::table('transfers')->where('transfer_id', $transfers_id)->update([
                'author' => $author->id,
                'status' => 1,
                'updated_at' => date(now()),
            ]);
            DB::commit();
            return redirect()->back()->with('alert_success', 'Xử lý lệnh chuyển tiền thành công.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('alert_error', 'Xãy ra lỗi.');
        }
    }

    public function getCancelled(Request $request, $transfers_id)
    {
        Gate::allows('modules', 'finance_transfers_edit');

        $transfer = DB::table('transfers')->where('transfer_id', $transfers_id)->where('status', 0)->first();
        if (is_null($transfer)) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }

        $user = Auth::user();
        if (!$request->has('twofa_code')) {
            return redirect()->back()->with('alert_error', 'Vui lòng nhập mã Authy của bạn.');
        }

        if ($user->google2fa_enable) {
            $twofa = new Twofa();
            $passSecret = $user->google2fa_secret;
            $valid = $twofa->verifyCode($passSecret, $request->twofa_code);
            if (!$valid) {
                return redirect()->back()->with('alert_error', 'Mã bảo mật của bạn không đúng. Xin vui lòng thử lại.');
            }
        }

        DB::table('transfers')->where('transfer_id', $transfers_id)->update([
            'status' => 2,
            'author' => $user->id,
            'updated_at' => date(now())
        ]);

        // add Transaction history
        TransactionHistory::historyLiveBalance($transfer->userid, 'REFUND_TRANSFER', $transfer->amount, 'primary_balance', $transfers_id);
        DB::table('users')->where('id', $transfer->userid)->increment('primary_balance', $transfer->amount);
        return redirect()->back()->with('alert_success', 'Bạn đã từ chối cho lệnh chuyển tiền này.');
    }
}
