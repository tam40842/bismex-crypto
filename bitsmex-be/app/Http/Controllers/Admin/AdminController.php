<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\Vuta\CryptoMap;
use App\User;
use Gate;
use App\Twofa;
use Session;
use Validator;

class AdminController extends Controller
{
    public function postLoginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if($user) {
            session(['user' => $user]);
        }

        if (is_null($user)) {
            return redirect()->back()->with('alert_error', 'Email or Password is not valid.');
        }
        if (!$user->status) {
            return redirect()->back()->with('alert_error', 'Your account is not verified email.');
        }
        if ($user->status == 2) {
            return redirect()->back()->with('alert_error', 'Your account has been banned.');
        }

        $credentials = $request->only('email', 'password');

        session(['credentials' => $credentials]);

        if ($user->google2fa_enable == 1) {
            return redirect()->route('get_login_2fa');
        } elseif ($user->google2fa_enable == 0 && Auth::attempt($credentials)) {
            session()->forget('user');
            session()->forget('credentials');
            return redirect('/admin');
        }

        return redirect()->back()->with('alert_error', 'Đăng nhập thất bại.');
    }
    public function getLogin2FaAdmin() {
        return view('2fa');
    }

    public function postLogin2FaAdmin(Request $request)
    {
        $user = session('user');
        $credentials = session('credentials');
        $validator = Validator::make($request->all(), ['twofa_code' => 'required|string|min:6|max:6']);
        if ($validator->fails()) {
            return redirect()->back()->with('alert_error', 'Mã 2FA không hợp lệ.');
        }
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if(!$valid) {
            return redirect()->back()->with('alert_error', 'Mã 2FA không chính xác.');
        } else {
            if(Auth::attempt($credentials)) {
                session()->forget('user');
                session()->forget('credentials');
                return redirect('/admin');
            }
            return redirect()->back()->with('alert_error', 'Đã xảy ra lỗi vui lòng kiểm tra lại.');
        }
        
    }

    public function Logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function index()
    {
        Gate::allows('modules', 'dashboard_access');

        $data_volume_date = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.created_at', '>=', Carbon::now()->startOfMonth())->where('orders.type', 'live')
            ->groupBy('date')
            ->orderBy('orders.created_at', 'ASC')
            ->get(array(
                DB::raw('Date(orders.created_at) as date'),
                DB::raw('SUM(orders.amount) as "volume"')
            ));
        $month = Carbon::now()->startOfMonth();
        $get_orders = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.created_at', '>=', $month)->where('orders.status', '!=', 0)
            ->groupBy('_day')
            ->orderBy('orders.created_at', 'ASC')
            ->get(array(
                DB::raw('Date(orders.created_at) as _day'),
                DB::raw('IFNULL(IFNULL(sum(case when orders.status = 2 then orders.amount else 0 end), 0) - IFNULL(sum(case when orders.status = 1 then orders.amount else 0 end), 0), 0) as profit')
            ));
        $get_users = DB::select(DB::raw("SELECT count(id) as total, DATE_FORMAT(created_at, '%Y-%m-%d') as _day FROM users where admin_setup = 0 and (created_at between '" . Carbon::now()->startOfMonth() . "' AND '" . Carbon::now()->endOfMonth() . "') GROUP BY _day"));

        $total_orders = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.status', '!=', 0)->where('orders.type', 'live')->select(
            DB::raw('COUNT(orders.id) as total_orders, 
                    SUM(case when orders.status = 1 then orders.amount - (orders.amount * orders.profit_percent/100) else 0 end) as total_fee, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else 0 end) as total_win, 
                    SUM(case when orders.status = 2 then orders.amount else 0 end) as total_lose, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else orders.amount end) as total_volume')
        )->first();
        $total_deposit = DB::table('deposit')->where('status', 1)->sum('total');
        $total_withdraw = DB::table('withdraw')->where('status', 1)->select(DB::raw('SUM(amount) as total_withdraw, SUM(amount-total) as total_fee_withdraw'))->first();
        $total_commissions = DB::table('commissions')->where('status', 1)->sum('amount');
        $users = DB::table('users')->where('admin_setup', 0)->select(DB::raw('sum(live_balance) as total_live_balance, sum(primary_balance) as total_primary_balance, count(id) as total_user'))->first();

        //where today
        $setting =  $this->get_settings(['profit_reset_time']);
        $resetDay = date('Y-m-d', strtotime($setting['profit_reset_time']));
        $now = now()->format('Y-m-d');
        $time = ($resetDay == $now) ? $setting['profit_reset_time'] : Carbon::now()->startOfDay()->format('Y-m-d H:i:s');

        $total_orders_date = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.status', '!=', 0)->where('orders.type', 'live')->where('orders.created_at', '>=', $time)->select(
            DB::raw('COUNT(orders.id) as total_orders, 
                    SUM(case when orders.status = 1 then orders.amount - (orders.amount * orders.profit_percent/100) else 0 end) as total_fee, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else 0 end) as total_win, 
                    SUM(case when orders.status = 2 then orders.amount else 0 end) as total_lose, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else orders.amount end) as total_volume')
        )->first();
        $total_deposit_date = DB::table('deposit')->where('status', 1)->where('created_at', '>=', $time)->sum('total');
        $total_withdraw_date = DB::table('withdraw')->where('status', 1)->where('created_at', '>=', $time)->select(DB::raw('SUM(amount) as total_withdraw, SUM(amount-total) as total_fee_withdraw'))->first();
        $total_commissions_date = DB::table('commissions')->where('status', 1)->where('created_at', '>=', $time)->sum('amount');
        $users_date = DB::table('users')->where('admin_setup', 0)->where('created_at', '>=', $time)->select(DB::raw('sum(live_balance) as total_live_balance, sum(primary_balance) as total_primary_balance, count(id) as total_user'))->first();

        $user_trade24h = DB::table('orders')->where('orders.type', 'live')->where('orders.status', '!=', 0)->whereDate('orders.created_at', now())
            ->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)
            ->select(
                'orders.*',
                'users.username',
                'users.live_balance',
                DB::raw('count(case when orders.status = 2 then orders.id end) as lose_count, 
                count(case when orders.status = 1 then orders.id end) as win_count, 
                IFNULL(sum(case when orders.status = 2 then orders.amount else 0 end),0) as lose_total, 
                IFNULL(sum(case when orders.status = 1 then orders.amount*profit_percent/100 else 0 end), 0) as win_total')
            )
            ->groupBy('users.username')
            ->get();
        foreach ($user_trade24h as $key => $value) {
            if ($value->win_total - $value->lose_total < 0) {
                $user_trade24h[$key]->user_type = 'lose';
            } else {
                $user_trade24h[$key]->user_type = 'win';
            }
        }
        $data = [
            'stastics_total' => [
                'users' => [
                    'icon' => 'fa fa-users',
                    'type' => 'amount',
                    'name' => 'Tổng user',
                    'total' => $users->total_user
                ],
                'orders' => [
                    'icon' => 'fa fa-balance-scale',
                    'type' => 'amount',
                    'name' => 'Tổng lệnh đặt',
                    'total' => $total_orders->total_orders
                ],
                'live_balance' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng tiền tài khoản Live',
                    'total' => $users->total_live_balance
                ],
                'primary_balance' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng tiền tài khoản Primary',
                    'total' => $users->total_primary_balance
                ],
                'deposit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng nạp tiền',
                    'total' => $users->total_live_balance + $users->total_primary_balance
                ],
                'withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng rút tiền',
                    'total' => $total_withdraw->total_withdraw,
                ],
                'fee_withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng phí rút tiền',
                    'total' => $total_withdraw->total_fee_withdraw,
                ],
                'volume' => [
                    'icon' => 'fa fa-bar-chart',
                    'type' => 'money',
                    'name' => 'Tổng khối lượng',
                    'total' => $total_orders->total_volume
                ],
                'total_win' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng thắng',
                    'total' => $total_orders->total_win
                ],
                'total_lose' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng thua',
                    'total' => $total_orders->total_lose
                ],
                'total_profit_fee' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng phí giao dịch',
                    'total' => $total_orders->total_fee
                ],
                'total_profit_trade' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng lợi nhuận giao dịch',
                    'total' => ($total_orders->total_win - $total_orders->total_lose + $total_orders->total_fee) * (-1)
                ],
                'total_commissions' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng chính sách',
                    'total' => $total_commissions
                ],
            ],
            'stastics_day' => [
                'users' => [
                    'icon' => 'fa fa-users',
                    'type' => 'amount',
                    'name' => 'Tổng user',
                    'total' => $users_date->total_user
                ],
                'orders' => [
                    'icon' => 'fa fa-balance-scale',
                    'type' => 'amount',
                    'name' => 'Tổng lệnh đặt',
                    'total' => $total_orders_date->total_orders
                ],
                'deposit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng nạp tiền',
                    'total' => $total_deposit_date
                ],
                'withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng rút tiền',
                    'total' => $total_withdraw_date->total_withdraw,
                ],
                'fee_withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng phí rút tiền',
                    'total' => $total_withdraw_date->total_fee_withdraw,
                ],
                'volume' => [
                    'icon' => 'fa fa-bar-chart',
                    'type' => 'money',
                    'name' => 'Tổng khối lượng',
                    'total' => $total_orders_date->total_volume
                ],
                'total_win' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng thắng',
                    'total' => $total_orders_date->total_win
                ],
                'total_lose' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng thua',
                    'total' => $total_orders_date->total_lose
                ],
                'total_profit_fee' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng phí giao dịch',
                    'total' => $total_orders_date->total_fee
                ],
                'total_profit_trade' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng lợi nhuận giao dịch',
                    'total' => ($total_orders_date->total_win - $total_orders_date->total_lose + $total_orders_date->total_fee) * (-1)
                ],
                'total_commissions' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng chính sách',
                    'total' => $total_commissions_date
                ],
            ],
            'order_label' => [],
            'order_data' => [],
            'user_label' => [],
            'user_data' => []
        ];

        // $data['stastics_total']['profit']['total'] = $data['stastics_total']['deposit']['total'] - $data['stastics_total']['withdraw']['total'] - ($data['stastics_total']['live_balance']['total'] );
        // $data['stastics_day']['total_profit_day']['total'] = $deposit_day - $withdraw_day - ($data['stastics_total']['live_balance']['total'] );

        foreach ($get_orders as $key => $value) {
            $data['order_label'][$key] = $value->_day;
            $data['order_data'][$key] = $value->profit;
        }
        foreach ($get_users as $key => $value) {
            $data['user_label'][$key] = $value->_day;
            $data['user_data'][$key] = $value->total;
        }
        $data['orders_volume_date'] = $data_volume_date->pluck('date');
        $data['orders_volume'] = json_encode($data_volume_date->pluck('volume')->toArray());
        $data['user_trade24h'] = $user_trade24h;

        return view('admin.index', $data);
    }

    public function update_code($userid)
    {
        $userF1 = User::find($userid);
        if (!is_null($userF1)) {
            if ($userF1->sponsor_id > 0) {
                $userF0 = User::find($userF1->sponsor_id);
            }
            $code = strtoupper(uniqid($userF1->id));
            DB::table('users')->where('admin_setup', 0)->where('id', $userF1->id)->update([
                'sponsor_ref' => $code,
                'sponsor_code' => isset($userF0) == false ? $code : $userF0->sponsor_code . '-' . $code,
                'sponsor_level' =>  isset($userF0) == false ? 0 : $userF0->sponsor_level + 1,
            ]);
            $user_children = DB::table('users')->where('admin_setup', 0)->where('sponsor_id', $userF1->id)->get();
            if (count($user_children) > 0) {
                foreach ($user_children as $value) {
                    $this->update_code($value->id);
                }
            }
        }
    }

    public function data()
    {
        return DB::select('SELECT count(*) as Total, a.userid, b.username, b.live_balance, a.action,a.status, a.created_at FROM `orders` as a,`users` as b 
                WHERE a.userid = b.id
                GROUP BY a.userid, a.action, a.status
                HAVING a.status <> 0 and DATE(a.created_at) = CURRENT_DATE()');
    }
}
