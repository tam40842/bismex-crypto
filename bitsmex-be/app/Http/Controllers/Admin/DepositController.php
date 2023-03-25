<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use App\Jobs\SendEmail;
use Carbon\Carbon;
use App\Http\Controllers\Vuta\CryptoMap;
use Gate;
use App\TransactionHistory;

class DepositController extends Controller
{
    public function index(Request $request) {
        Gate::allows('modules', 'finance_deposit_access');

        $deposit = DB::table('deposit')->leftjoin('users', 'deposit.userid', '=', 'users.id')
        ->where('users.admin_setup', 0)
        ->where('deposit.action', 'DEPOSIT')
        ->select('deposit.*', 'users.username as username')
        ->orderBy('deposit.id', 'desc')->paginate(100);
        foreach($deposit as $key => $value) {
            $deposit[$key]->txhash_url = CryptoMap::hashLink($value->symbol, $value->txhash);
        }
        $data = [
            'deposit' => $deposit,
            'deposit_status' => $this->deposit_status(),
            'currencies' => DB::table('currencies')->get(),
            'sum' => []
        ];
        foreach($data['currencies'] as $key => $value) {
            $data['sum'][$value->symbol] = DB::table('deposit')->where('symbol', $value->symbol)->whereNotNull('txhash')->where('status', 1)->sum('total');
        }
        $data['sum']['MANUALLY'] = DB::table('deposit')->whereNull('txhash')->where('status', 1)->sum('total');
        return view('admin.deposit.index', $data);
    }

    public function getFilters(Request $request) {
        $request->paginate = 100;
        if($request->has('start_day') && $request->has('end_day')) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            if(date($request->end_day) == date('Y-m-d')) {
                $end_day = date('Y-m-d H:i:s');
            }else {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            }
            if($request->symbol == "Full") {
                $deposit = DB::table('deposit')->leftjoin('users', 'users.id', '=', 'deposit.userid')
                ->whereBetween('deposit.created_at', [$start_day, $end_day])
                ->where('users.admin_setup', 0)
                ->where('deposit.status', $request->status)
                ->select('deposit.*', 'users.username as username')->orderBy('deposit.id', 'desc')->paginate($request->paginate);
            }else {
                $deposit = DB::table('deposit')->leftjoin('users', 'users.id', '=', 'deposit.userid')
                ->whereBetween('deposit.created_at', [$start_day, $end_day])
                ->where('users.admin_setup', 0)
                ->where('deposit.symbol', $request->symbol)
                ->where('deposit.status', $request->status)
                ->select('deposit.*', 'users.username as username')->orderBy('deposit.id', 'desc')->paginate($request->paginate);
            }
            foreach($deposit as $key => $value) {
                $deposit[$key]->txhash_url = CryptoMap::hashLink($value->symbol, $value->txhash);
            }
            $currencies  = DB::table('currencies')->get();
            $data = [
                'deposit' => $deposit,
                'currencies' => $currencies,
                'deposit_status' => $this->deposit_status(),
                'sum' => [],
                'filter' => [
                    'start_day' => $request->start_day,
                    'end_day' => $request->end_day,
                    'symbol' => $request->symbol,
                    'status' => $request->status,
                    'paginate' => $request->paginate,
                ]
            ];
            foreach($currencies as $key => $value) {
                $data['sum'][$value->symbol] = DB::table('deposit')->whereBetween('created_at', [$start_day, $end_day])->whereNotNull('txhash')->where('symbol', $value->symbol)->sum('amount');
            }
            $data['sum']['MANUALLY'] = DB::table('deposit')->whereBetween('created_at', [$start_day, $end_day])->whereNull('txhash')->where('status', 1)->sum('total');
            return view('admin.deposit.index', $data);
        }
        return redirect()->route('admin.deposit');
    }

    public function filterbyUser(Request $request, $userid) {
        $today = date('Y-m-d H:i:s');
        $user = User::find($userid);
        if(is_null($user)) {
            return redirect()->route('admin.users')->with('alert_error', 'Tài khoản không tồn tại.');
        }
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $deposit = DB::table('deposit')->leftjoin('users', 'users.id', '=', 'deposit.userid')
        ->where('deposit.userid', $user->id)->where('users.admin_setup', 0);
        if($request->has('date_from') && $request->has('date_to')) {
            $deposit = $deposit->whereBetween('deposit.created_at', [$date_from, $date_to]);
        }
        $deposit = $deposit->select('deposit.*', 'users.username as username')->orderBy('deposit.id', 'desc')->paginate(100);
        
        $data = [
            'deposit' => $deposit,
            'currencies' => Currencies::all(),
            'deposit_status' => $this->deposit_status(),
            'sum' => []
        ];
        foreach(Currencies::all() as $key => $value) {
            $sum = DB::table('deposit')->where('userid', $user->id)->where('symbol', $value->symbol);
            if($request->has('date_from') && $request->has('date_to')) {
                $sum = $sum->whereBetween('deposit.created_at', [$date_from, $date_to]);
            }
            $data['sum'][$value->symbol] = $sum->sum('amount');
        }
        
        $vnd_sum = DB::table('deposit')->where('userid', $user->id)->where('symbol', 'VND');
        if($request->has('date_from') && $request->has('date_to')) {
            $vnd_sum = $vnd_sum->whereBetween('deposit.created_at', [$date_from, $date_to]);
        }
        return view('admin.deposit.index', $data);
    }

    public function getAdd(Request $request) {
        Gate::allows('modules', 'finance_deposit_add');

        $data = [
            'currencies' => DB::table('currencies')->get(),
        ];
        return view('admin.deposit.add', $data);
    }

    public function postAdd(Request $request) {
        Gate::allows('modules', 'finance_deposit_add');

        $this->validate($request, [
            'recipient' => 'required|string',
            'amount' => 'required|min:0'
        ]);
        $recipient = $request->recipient;
        $user = DB::table('users')->where('admin_setup', 0)->where(function($query) use ($recipient) {
            $query->where('username', $recipient)->orWhere('email', $recipient);
        })->first();
        if(is_null($user)) {
            return redirect()->back()->with('alert_error', 'Người nhận không tồn tại.');
        }
        $create = [
            'deposit_id' => strtoupper(uniqid('D')),
            'action' => 'DEPOSIT',
            'userid' => $user->id,
            'symbol' => 'USDT',
            'amount' => (double)$request->amount,
            'total' => (double)$request->amount,
            'status' => 1,
            'type' => 'deposit',
            'author' => Auth::user()->id,
            'created_at' => date(now()),
            'updated_at' => date(now())
        ];
        DB::table('deposit')->insert($create);
        
        // Save transaction history
        TransactionHistory::historyLiveBalance($user->id, 'DEPOSIT', $request->amount, 'primary_balance', $create['deposit_id']);

        DB::table('users')->where('id', $user->id)->increment('primary_balance', $request->amount);
        // SendEmail::dispatch($user->email, 'Tài khoản của bạn vừa được ghi có', 'deposit', ['user' => $user, 'deposit' => $create]);
        
        return redirect()->route('admin.deposit')->with('alert_success', 'Tạo lệnh nạp tiền thành công.');
    }

    public function getEdit($deposit_id) {
        Gate::allows('modules', 'finance_deposit_edit');

        $deposit = DB::table('deposit')->where('deposit_id', $deposit_id)->first();
        if(is_null($deposit)) {
            return redirect()->route('admin.deposit')->with('alert_error', 'Deposit does not exist.');
        }
        $data = [
            'deposit' => $deposit,
            'deposit_status' => $this->deposit_status(),
            'user' => User::find($deposit->userid),
        ];
        return view('admin.deposit.edit', $data);
    }

    public function postSearch(Request $request) {
        $this->validate($request, [
            'search_text' => 'required'
        ]);
        $search_text = $request->search_text;
        $deposit = DB::table('deposit')->leftjoin('users', 'users.id', '=', 'deposit.userid')->where('users.admin_setup', 0)->where(function($query) use ($search_text) {
            $query->where('deposit.deposit_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('deposit.amount', 'LIKE', '%'.$search_text.'%')
            ->orWhere('deposit.txhash', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.ref_id', 'LIKE', '%'.$search_text.'%');
        })->select('deposit.*', 'users.username as username')->orderBY('deposit.id', 'desc')->paginate(100);
        foreach($deposit as $key => $value) {
            $deposit[$key]->txhash_url = CryptoMap::hashLink($value->symbol, $value->txhash);
        }
        $data = [
            'deposit' => $deposit,
            'deposit_status' => $this->deposit_status(),
            'currencies' => DB::table('currencies')->get(),
            'sum' => []
        ];
        foreach($data['currencies'] as $key => $value) {
            $data['sum'][$value->symbol] = DB::table('deposit')->where('symbol', $value->symbol)->whereNotNull('txhash')->where('status', 1)->sum('total');
        }
        $data['sum']['MANUALLY'] = DB::table('deposit')->whereNull('txhash')->where('status', 1)->sum('total');
        return view('admin.deposit.index', $data)->render();
    }
}
