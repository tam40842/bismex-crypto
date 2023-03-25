<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Withdraw;
use App\Twofa;
use App\User;
use App\Events\Withdraw as EventWithdraw;
use App\Jobs\SendEmail;
use DB;
use Auth;
use Carbon\Carbon;
use Gate;
use App\TransactionHistory;

class WithdrawController extends Controller
{
    public function index(Request $request) {
        Gate::allows('modules', 'finance_withdraw_access');

        $withdraw = DB::table('withdraw')->leftjoin('users', 'withdraw.userid', '=', 'users.id')
        ->where('users.admin_setup', 0)
        ->select('withdraw.*', 'users.username as username')
        ->orderBy('withdraw.id', 'desc')->paginate(100);
        $stastics_usd = DB::table('withdraw')->where('symbol', '!=', 'VNDC')
        ->select(DB::raw('IF((fee != 0),(amount * fee), withdraw.amount) as withdraw_fee, withdraw.*'))->get();
        $completed = 0;
        $cancelled = 0;
        $pending = 0;
        $fee = 0;
        foreach($stastics_usd as $key => $value) {
            switch($value) {
                case $value->status == 1:
                    $completed += $value->withdraw_fee;
                    $fee += $value->fee;
                    break;
                case $value->status == 2:
                    $cancelled += $value->withdraw_fee;
                    break;
                case $value->status == 0:
                    $pending += $value->withdraw_fee;
                    break;
            }
        }
        $data = [
            'withdraw' => $withdraw,
            'withdraw_status' => $this->withdraw_status(),
            'currencies' => DB::table('currencies')->get(),
            'sum' => [],
            'stastics_usd' => [
                'completed' => $completed,
                'cancelled' => $cancelled,
                'pending' => $pending,
                'fee' => $fee,
            ],
        ];
        foreach($data['currencies'] as $key => $value) {
            $data['sum'][$value->symbol] = DB::table('withdraw')->where('symbol', $value->symbol)->where('status', 1)->sum('amount');
        }
        return view('admin.withdraw.index', $data);
    }

    public function getFilters(Request $request) {
        if($request->has('start_day') && $request->has('end_day')) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            if(date($request->end_day) == date('Y-m-d')) {
                $end_day = date('Y-m-d H:i:s');
            }else {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            }
            if($request->symbol == "Full") {
                $withdraw = DB::table('withdraw')->leftjoin('users', 'users.id', '=', 'withdraw.userid')
                ->whereBetween('withdraw.created_at', [$start_day, $end_day])
                ->where('users.admin_setup', 0)
                ->where('withdraw.status', $request->status)
                ->select('withdraw.*', 'users.username as username')->orderBy('withdraw.id', 'desc')->paginate($request->paginate);
            }else {
                $withdraw = DB::table('withdraw')->leftjoin('users', 'users.id', '=', 'withdraw.userid')
                ->whereBetween('withdraw.created_at', [$start_day, $end_day])
                ->where('users.admin_setup', 0)
                ->where('withdraw.symbol', $request->symbol)
                ->where('withdraw.status', $request->status)
                ->select('withdraw.*', 'users.username as username')->orderBy('withdraw.id', 'desc')->paginate($request->paginate);
            }
            $stastics_usd = DB::table('withdraw')->where('symbol', '!=', 'VNDC')
            ->select(DB::raw('IF((fee != 0),(amount * fee), withdraw.amount) as withdraw_fee, withdraw.*'))->get();
            $completed = 0;
            $cancelled = 0;
            $pending = 0;
            $fee = 0;
            foreach($stastics_usd as $key => $value) {
                switch($value) {
                    case $value->status == 1:
                        $completed += $value->withdraw_fee;
                        $fee += $value->fee;
                        break;
                    case $value->status == 2:
                        $cancelled += $value->withdraw_fee;
                        break;
                    case $value->status == 0:
                        $pending += $value->withdraw_fee;
                        break;
                }
            }
            $data = [
                'withdraw' => $withdraw,
                'currencies' => DB::table('currencies')->get(),
                'withdraw_status' => $this->withdraw_status(),
                'sum' => [],
                'stastics_usd' => [
                    'completed' => $completed,
                    'cancelled' => $cancelled,
                    'pending' => $pending,
                    'fee' => $fee,
                ],
                'stastics_vndc' => [
                    'completed' => DB::table('withdraw')->where('symbol','VNDC')->where('status', 1)->sum('amount'),
                    'cancelled' => DB::table('withdraw')->where('symbol','VNDC')->where('status', 2)->sum('amount'),
                    'pending' => DB::table('withdraw')->where('symbol','VNDC')->where('status', 0)->sum('amount'),
                    'fee' => DB::table('withdraw')->where('symbol','VNDC')->where('status', 1)->sum('fee'),
                ],
                'filter' => [
                    'start_day' => $request->start_day,
                    'end_day' => $request->end_day,
                    'symbol' => $request->symbol,
                    'status' => $request->status,
                    'paginate' => $request->paginate,
                ]
            ];
            foreach(DB::table('currencies')->get() as $key => $value) {
                $data['sum'][$value->symbol] = DB::table('withdraw')->whereBetween('created_at', [$start_day, $end_day])->where('symbol', $value->symbol)->sum('amount');
            }
            $data['sum']['VND'] = DB::table('withdraw')->whereBetween('created_at', [$start_day, $end_day])->sum('total');
            return view('admin.withdraw.index', $data);
        }
        return redirect()->route('admin.withdraw');
    }

    public function filterbyUser(Request $request, $userid) {
        $today = date('Y-m-d H:i:s');
        $user = User::find($userid);
        if(is_null($user)) {
            return redirect()->route('admin.users')->with('alert_error', 'Tài khoản không tồn tại.');
        }
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $withdraw = DB::table('withdraw')->leftjoin('users', 'users.id', '=', 'withdraw.userid')
        ->where('users.admin_setup', 0)
        ->where('withdraw.userid', $user->id);
        if($request->has('date_from') && $request->has('date_to')) {
            $withdraw = $withdraw->whereBetween('withdraw.created_at', [$date_from, $date_to]);
        }
        $withdraw = $withdraw->select('withdraw.*', 'users.username as username')->orderBy('withdraw.id', 'desc')->paginate(10);
        
        $data = [
            'withdraw' => $withdraw,
            'currencies' => DB::table('currencies')->get(),
            'withdraw_status' => $this->withdraw_status(),
            'sum' => []
        ];
        foreach(DB::table('currencies')->get() as $key => $value) {
            $sum = DB::table('withdraw')->where('userid', $user->id)->where('symbol', $value->symbol);
            if($request->has('date_from') && $request->has('date_to')) {
                $sum = $sum->whereBetween('withdraw.created_at', [$date_from, $date_to]);
            }
            $data['sum'][$value->symbol] = $sum->sum('amount');
        }
        
        $vnd_sum = DB::table('withdraw')->where('userid', $user->id)->where('symbol', 'VND');
        if($request->has('date_from') && $request->has('date_to')) {
            $vnd_sum = $vnd_sum->whereBetween('withdraw.created_at', [$date_from, $date_to]);
        }
        $data['sum']['VND'] = $vnd_sum->sum('total');
        return view('admin.withdraw.index', $data);
    }

    public function getAdd(Request $request) {
        Gate::allows('modules', 'finance_withdraw_add');

        $data = [
            'currencies' => DB::table('currencies')->get(),
        ];

        return view('admin.withdraw.add', $data);
    }

    public function postAdd(Request $request) {
        Gate::allows('modules', 'finance_withdraw_add');

        $this->validate($request, [
            'recipient' => 'required|string',
            'amount' => 'required|min:0'
        ]);
        $recipient = $request->recipient;
        $user = DB::table('users')->where('admin_setup', 0)->where(function($query) use ($recipient) {
            $query->where('username', $recipient)->orWhere('email', $recipient);
        })->first();
        if(is_null($user)) {
            return redirect()->back()->with('alert_error', 'Tài khoản đích không tồn tại.');
        }
        if($request->amount > $user->wallet_balance && in_array('USDT', json_decode($user->markets))) {
            return redirect()->back()->with('alert_error', 'Số dư của tài khoản ngày không đủ để trừ.');
        }
        DB::table('users')->where('admin_setup', 0)->where('id', $user->id)->decrement('primary_balance', $request->amount);
        $create = DB::table('withdraw')->insertGetId([
            'action' => 'WITHDRAW',
            'withdraw_id' => strtoupper(uniqid('W')),
            'userid' => $user->id,
            'symbol' => 'USDT',
            'amount' => (double)$request->amount,
            'total' => (double)$request->amount,
            'status' => 1,
            'author' => Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $withdraw_user = DB::table('withdraw')->find($create);
        SendEmail::dispatch($user->email, 'Withdrawal Successful', 'withdraw', ['user' => $user, 'withdraw' => $withdraw_user]);
        return redirect()->route('admin.withdraw')->with('alert_success', 'Tạo lệnh trừ tiền thành công.');
    }

    public function getEdit($withdraw_id) {
        Gate::allows('modules', 'finance_withdraw_edit');

        $withdraw = DB::table('withdraw')->where('withdraw_id', $withdraw_id)->first();
        if(is_null($withdraw)) {
            return redirect()->route('admin.withdraw')->with('alert_error', 'withdraw does not exist.');
        }
        
        $data = [
            'withdraw' => $withdraw,
            'withdraw_status' => $this->withdraw_status(),
            'user' => User::find($withdraw->userid),
        ];
        return view('admin.withdraw.edit', $data);
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $withdraw = DB::table('withdraw')->leftjoin('users', 'users.id', '=', 'withdraw.userid')->where('users.admin_setup', 0)->where(function($query) use ($search_text) {
            $query->where('withdraw.withdraw_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('withdraw.amount', 'LIKE', '%'.$search_text.'%')
            ->orWhere('withdraw.txhash', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.ref_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('withdraw.output_address', 'LIKE', '%'.$search_text.'%');
        })->select('withdraw.*', 'users.username as username')->orderBY('withdraw.id', 'desc')->paginate(100);
        
        $data = [
            'withdraw' => $withdraw,
            'withdraw_status' => $this->withdraw_status()
        ];
        return view('admin.withdraw._item', $data)->render();
    }


    public function getApproved(Request $request, $withdraw_id) {
        Gate::allows('modules', 'finance_withdraw_edit');

        $withdraw = DB::table('withdraw')->where('withdraw_id', $withdraw_id)->where('status', 0)->first();
        if(is_null($withdraw)) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }
        if($withdraw->status != 0) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }
        $user = Auth::user();
        if(!$request->has('twofa_code')) {
            return redirect()->back()->with('alert_error', 'Vui lòng nhập mã Authy của bạn.');
        }
        if($user->google2fa_enable) {
            $twofa = new Twofa();
            $passSecret = ($user->google2fa_secret);
            $valid = $twofa->verifyCode($passSecret, $request->twofa_code);
            if(!$valid) {
                return redirect()->back()->with('alert_error', 'Mã bảo mật của bạn không đúng. Xin vui lòng thử lại.');
            }
        }
        
        // Xử lý rút tiền tự động
        // $coin = "\\App\\Currencies\\".$withdraw->symbol;
        // $coin = new $coin;
        // $memo = 'Withdraw from '.$withdraw->withdraw_id;
        // $transfer = $coin->transfer($withdraw->total, $withdraw->output_address,$memo);
        // if($transfer['status'] == true) {
            DB::table('withdraw')->where('withdraw_id', $withdraw_id)->update([
                'author' => $user->id,
                // 'txhash' => $transfer['txhash'],
                'status' => 1,
                'updated_at' => date(now()),
            ]);
        // }
        // if($transfer['status'] == false) {
        //     DB::table('withdraw')->where('withdraw_id', $withdraw_id)->update([
        //         'status' => 3,
        //         'updated_at' => date(now()),
        //     ]);
        // }
        return redirect()->back()->with('alert_success', 'Xử lý lệnh rút tiền thành công.');
    }

    public function getCancelled(Request $request, $withdraw_id) {
        Gate::allows('modules', 'finance_withdraw_edit');
        
        $withdraw = DB::table('withdraw')->where('withdraw_id', $withdraw_id)->where('status', 0)->first();
        if(is_null($withdraw)) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }
        if($withdraw->status != 0) {
            return redirect()->back()->with('alert_error', 'Access denied.');
        }
        $user = Auth::user();
        if(!$request->has('twofa_code')) {
            return redirect()->back()->with('alert_error', 'Vui lòng nhập mã Authy của bạn.');
        }
        if($user->google2fa_enable) {
            $twofa = new Twofa();
            $passSecret = $user->google2fa_secret;
            $valid = $twofa->verifyCode($passSecret, $request->twofa_code);
            if(!$valid) {
                return redirect()->back()->with('alert_error', 'Mã bảo mật của bạn không đúng. Xin vui lòng thử lại.');
            }
        }
        
        DB::table('withdraw')->where('withdraw_id', $withdraw_id)->update([
            'status' => 2,
            'author' => $user->id,
            'updated_at' => date(now())
        ]);

        // add Transaction history
        TransactionHistory::historyLiveBalance($withdraw->userid,'REFUND', $withdraw->amount*$withdraw->rate, 'primary_balance', $withdraw_id);

        // DB::table('user_balance')->where('userid', $withdraw->userid)->increment($withdraw->symbol, $withdraw->amount);
        DB::table('users')->where('id', $withdraw->userid)->increment('primary_balance', $withdraw->amount);
        return redirect()->back()->with('alert_success', 'Bạn đã từ chối cho lệnh rút tiền này.');
    }
}
