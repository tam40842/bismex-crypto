<?php

namespace Modules\CopyTrading\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Auth;
use Carbon\Carbon;
use Validator;
use App\Twofa;

class CopyTradingController extends Controller
{
    public $expert_profit = 15;
    public $expert_min_volume = 100000;
    public $expert_balance = 10000;
    
    public function ProfileTrader() {
        $user = Auth::user();
        $supper_trader = DB::table('copy_trader')->where('userid', $user->id)->first();
        return response()->json([
            'status' => 200,
            'data' => [
                'volumeMonth' => $this->ProfitMonth($user->id)['volumeMonth'],
                'profit' => $this->ProfitMonth($user->id)['profit'],
                'is_register' => is_null($supper_trader) ? '' : $supper_trader->status,
                'balance' => $user->live_balance,
                'kyc' => $user->kyc_status == 2 ? 1 : 0,
            ]
        ]);
    }

    public function postRegistration(Request $request) {
        $validator = Validator::make($request->all(), [
            'min_invest' => 'required|numeric|min:0',
            'fee' => 'required|numeric|min:0',
            'display_name' => 'required|string|max:100|alpha_dash',
            'twofa_code' => 'required|string|max:6',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if(!$valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The two factor code is invalid.'
            ]);
        }
        
        $expert = DB::table('copy_trader')->where('userid', $user->id)->first();
        if(!is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert already exists.'
            ]);
        }

        $display_name_exist = DB::table('copy_trader')->where('name', $request->display_name)->first();
        if(!is_null($display_name_exist)) {
            return response()->json([
                'status' => 422,
                'message' => 'The display name has been existed.'
            ]);
        }

        if($user->kyc_status != 2) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account has not been verified documents.'
            ]);
        }

        $profile_trader = $this->ProfitMonth($user->id);
        if($profile_trader['volumeMonth'] < $this->expert_min_volume) {
            return response()->json([
                'status' => 422,
                'message' => 'Your trading volume is not enough'
            ]);
        }

        if($user->live_balance < $this->expert_balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance is not enough.'
            ]);
        }

        if($profile_trader['profit'] < $this->expert_profit) {
            return response()->json([
                'status' => 422,
                'message' => 'Last week profit is not enough.'
            ]);
        }

        DB::table('copy_trader')->insert([
            'userid' => $user->id,
            'email' => $user->email,
            'name' => (string)$request->display_name,
            'amount_min' => (double)$request->min_invest,
            'fee' => (double)$request->fee,
            'profit' => $profile_trader['profit'],
            'status' => 0,
            'created_at' => date(now()),
            'updated_at' => date(now())
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Thank you for signing up to become an expert. We are conducting verification of the information you have provided and will respond within the next 24 hours. Sincerely thank!'
        ]);
    }

    public function getList() {
        $trader = DB::table('copy_trader')->where('status', 1)->paginate(10);
        foreach($trader as $key => $value) {
            $check_user_follow = DB::table('copy_order')->where('user_expert', $value->userid)->where('user_follow', Auth::id())->first();
            $trader[$key] = $value;
            $trader[$key]->user_follow = !is_null($check_user_follow) ? $check_user_follow->user_follow : 0;
        }

        return response()->json([
            'status' => 200,
            'data' => $trader
        ]);
    }

    public function postCopying(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'user_expert' => 'required|integer',
            'min_copy' => 'required_with:min_copy|numeric|min:1|lt:max_copy',
            'max_copy' => 'required_with:max_copy|numeric|min:1|gt:min_copy',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }

        $user_expert = (int)$request->user_expert;
        $expert = DB::table('copy_trader')->where('userid', $user_expert)->where('status', 1)->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.'
            ]);
        }

        $user_follow = DB::table('copy_order')->where('user_expert', $expert->userid)->where('user_follow', $user->id)->first();
        if(!is_null($user_follow)) {
            return response()->json([
                'status' => 422,
                'message' => 'You signed up for this expert.'
            ]);
        }

        if($user->id == $expert->userid) {
            return response()->json([
                'status' => 422,
                'message' => 'You cannot copy yourself'
            ]);
        }

        $amount = (double)$request->amount;
        if($amount < $expert->amount_min) {
            return response()->json([
                'status' => 422,
                'message' => 'The invest amount min is $'.number_format($expert->amount_min)
            ]);
        }
        
        if($amount > $user->live_balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your live balance does not enough.'
            ]);
        }

        $user_follow = DB::table('copy_order')->where('user_expert', $expert->userid)->where('user_follow', $user->id)->first();
        if(!is_null($user_follow)) {
            return response()->json([
                'status' => 422,
                'message' => 'You have copied this expert.'
            ]);
        }

        DB::beginTransaction();
        try {
            $copier = DB::table('copy_order')->where('user_follow', $user->id)->where('user_expert', $expert->userid)->where('status', 1)->first();
            if(!is_null($copier)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Access denied.'
                ]);
            }
            $data = [
                'contract_id' => strtoupper(uniqid('C')),
                'user_expert' => $expert->userid,
                'user_follow' => $user->id,
                'balance' => $amount,
                'status' => 0,
                'created_at' => date(now()),
                'updated_at' => date(now())
            ];
            $data['min_copy'] = (double)$request->min_copy;
            $data['max_copy'] = (double)$request->max_copy;
            // if($request->has('stoploss') && $request->stoploss > 0) {
            //     $data['stoploss'] = (double)$request->stoploss;
            // }
            DB::table('copy_order')->insert($data);
            
            DB::table('users')->where('id', $user->id)->decrement('live_balance', $amount);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Copying successfully. Experts will approve in the next 24 hours.'
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Copying has an error.'
            ]);
        }
    }

    public function getListFollow() {
        $user = Auth::user();
        $listTrader = DB::table('copy_order')->where('copy_order.user_follow', $user->id)->where('copy_order.status', 1)
        ->join('copy_trader', 'copy_order.user_expert', '=', 'copy_trader.userid')
        ->where('copy_trader.status', 1)->select('copy_trader.*')->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => $listTrader
        ]);
    }

    public function getExpert(Request $request) {
        $user = Auth::user();
        $user_expert = strtoupper($request->user_expert);

        $expert = DB::table('copy_trader')->where('copy_trader.userid', $user_expert)
        ->where('copy_trader.status', 1)->join('copy_order', 'copy_trader.userid', 'copy_order.user_expert')
        ->where('copy_order.user_follow', $user->id)
        ->select('copy_trader.*', 'copy_order.balance as balance')->first();

        $historiesOrder = DB::table('copy_profit_histories')
                        ->where('copy_profit_histories.user_expert', $expert->userid)
                        ->where('copy_profit_histories.user_follow', $user->id)
                        ->join('orders', 'copy_profit_histories.orderid', '=', 'orders.orderid')
                        ->where('orders.status', '!=', 0)->where('orders.type', 'live');
        if(isset($request->filterData['date_from']) && isset($request->filterData['date_to'])) {
            $date_start = Carbon::create($request->date_from)->startOfDay();
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to)->endOfDay() : Carbon::now()->endOfDay();
            $historiesOrder = $historiesOrder->where('copy_profit_histories.created_at', '>=', $date_start)->where('copy_profit_histories.created_at', '<=', $date_end);
        }else {
            $historiesOrder = $historiesOrder->whereDate('copy_profit_histories.created_at', now());
        }
        if(isset($request->filterData['market'])) {
            $historiesOrder = $historiesOrder->where('orders.market_name', $request->filterData['market']);
        }
        $historiesOrder = $historiesOrder->select('copy_profit_histories.*', 
                        'orders.action as orders_action', 'orders.market_name as orders_market', 'orders.amount as orders_amount', 
                        'orders.profit_percent as orders_profit_percent', 'orders.open_price as orders_open_price', 'orders.close_price as orders_close_price', 
                        'orders.status as orders_status', 'orders.created_at as orders_created_at')
                        ->orderBy('orders.id', 'desc')->paginate(10);

        $StatisticCopy = DB::table('copy_profit_histories')->join('orders', 'copy_profit_histories.orderid', '=', 'orders.orderid')
                    ->where('copy_profit_histories.user_expert', $expert->userid)->where('copy_profit_histories.user_follow', $user->id)
                    ->select(DB::raw('COUNT(case when copy_profit_histories.status = 1 then copy_profit_histories.id end) as totalWin,
                                    COUNT(case when copy_profit_histories.status = 2 then copy_profit_histories.id end) as totalLose,
                                    SUM(case when copy_profit_histories.status = 1 then copy_profit_histories.total end) as totalVolumeWin,
                                    SUM(case when copy_profit_histories.status = 2 then orders.amount end) as totalVolumeLose'))->first();

        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => [
                'expert' => $expert,
                'totalWin' => $StatisticCopy->totalWin,
                'totalLose' => $StatisticCopy->totalLose,
                'totalProfit' => $StatisticCopy->totalVolumeWin - $StatisticCopy->totalVolumeLose,
                'historiesOrder' => $historiesOrder,
            ]
        ]);
    }

    public function postWallet(Request $request, $type) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = Auth::user();
        $amount = (double)$request->amount;

        $userExpert = DB::table('copy_trader')->where('userid', $request->user_expert)->where('status', 1)->first();
        $BalanceCopyTrading = DB::table('copy_order')->where('user_expert', $userExpert->userid)->where('user_follow', $user->id)->where('status', 1)->first();

        if(!is_null($BalanceCopyTrading)) {
            if($type == 'deposit') {
                if($amount > $user->live_balance) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Your balance is not enough.'
                    ]);
                }
                
                DB::beginTransaction();
                try {
                    DB::table('users')->where('id', $user->id)->decrement('live_balance', $amount);
                    DB::table('copy_order')->where('user_expert', $userExpert->userid)->where('user_follow', $user->id)->where('status', 1)->increment('balance', $amount);
                    
                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Your account has just been credited '.$amount,
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 422,
                        'message' => 'Deposit has error',
                    ]);
                }
            }else if($type == 'withdraw') {
                $balanceUser = $BalanceCopyTrading->balance - $amount;
                if($balanceUser < $userExpert->amount_min) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Minimum balance in copy trade wallet $'.number_format($userExpert->amount_min, 2)
                    ]);
                }
    
                DB::beginTransaction();
                try {
                    DB::table('users')->where('id', $user->id)->increment('live_balance', $amount);
                    DB::table('copy_order')->where('user_expert', $userExpert->userid)->where('user_follow', $user->id)->decrement('balance', $amount);
                    
                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'message' => 'You have a withdraw '.$amount,
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 422,
                        'message' => 'Withdraw has error',
                    ]);
                }
            }
        }
    }

    public function stopCopy(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_expert' => 'required|integer|min:0'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $user_expert = strtoupper($request->user_expert);
        $user = Auth::user();
        
        $expert = DB::table('copy_trader')->where('copy_trader.userid', $user_expert)
        ->where('copy_trader.status', 1)->join('copy_order', 'copy_trader.userid', 'copy_order.user_expert')
        ->where('copy_order.user_follow', $user->id)
        ->select('copy_trader.*', 'copy_order.balance as balance')
        ->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.'
            ]);
        }
        // $copy_date = Carbon::create($expert->copy_date); // Ngày bắt đầu vào gói
        // $now = Carbon::now();
        // $stop_fee = $now->diffInDays($copy_date) >= 30 ? 1 : 3; // Percent fee
        // $total = $amount - ($amount * $stop_fee / 100);
        DB::beginTransaction();
        try {
            // if(($expert->balance - $amount) >= 1) {
            //     DB::table('copier_fundings')->insert([
            //         'copier_id' => $user->id,
            //         'user_expert' => $expert->id,
            //         'amount' => '-'.$amount,
            //         'created_at' => date(now()),
            //         'updated_at' => date(now())
            //     ]);
            //     DB::table('copiers')->where('id', $expert->copy_id)->decrement('balance', $amount);
            // } else {
                
            // }
            DB::table('users')->where('id', $user->id)->increment('live_balance', $expert->balance);
            DB::table('copy_order')->where('user_expert', $user_expert)->where('user_follow', $user->id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Copying has been stopped.'
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Stop copy has an error.'
            ]);
        }
    }

    public function ProfitMonth($userid)
    {
        $user = DB::table('users')->where('id', $userid)->first();
        if(!is_null($user)) {
            $startWeek = Carbon::now()->subDay()->startOfMonth()->format('Y-m-d');
            $endOfWeek = Carbon::now()->subDay()->endOfMonth()->format('Y-m-d');

            $count_win = 0;
            $count_total = 0;
            $total_volume = 0;
            $order = DB::table('orders')->where('userid', $userid)->where('status', '!=', 0)
                    ->where('type', 'live')->where('created_at', '>=', $startWeek)
                    ->where('created_at', '<=', $endOfWeek)->get();

            foreach($order as $key => $value) {
                if($value->status == 1) {
                    $count_win ++;
                }
                $count_total ++;
                $total_volume += $value->amount;
            }
            $profit = $count_total > 0 ? round($count_win/$count_total *100, 2) : 0;

            $data = [
                'profit' => $profit,
                'volumeMonth' => $total_volume,
            ];
            return $data;
        }
    }

    public function getUsersPending() {
        $user = Auth::user();
        $user_pending = DB::table('copy_order')->where('copy_order.status', 0)
                    ->join('copy_trader', 'copy_order.user_expert', '=', 'copy_trader.userid')
                    ->where('copy_trader.userid', $user->id)
                    ->join('users', 'copy_order.user_follow', '=', 'users.id')
                    ->select('copy_order.*', 'users.username', 'copy_trader.fee')
                    ->paginate(4);

        return response()->json([
            'status' => 200,
            'data' => $user_pending
        ]);
    }

    public function getUserFollow() {
        $user = Auth::user();
        $user_follow = DB::table('copy_order')->where('copy_order.status', 1)
                    ->join('copy_trader', 'copy_order.user_expert', '=', 'copy_trader.userid')
                    ->where('copy_trader.userid', $user->id)
                    ->join('users', 'copy_order.user_follow', '=', 'users.id')
                    ->select('copy_order.*', 'users.username', 'copy_trader.fee')
                    ->paginate(4);

        return response()->json([
            'status' => 200,
            'data' => $user_follow
        ]);
    }

    public function postSetUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_follow' => 'required|integer|min:0',
            'action' => 'required'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $trader = DB::table('copy_trader')->where('userid', Auth::id())->first();
        $setUser = DB::table('copy_order')->where('user_expert', $trader->userid)->where('user_follow', $request->user_follow)->first();

        if(is_null($setUser)) {
            return response()->json([
                'status' => 422,
                'message' => 'User trader not found.'
            ]);
        }

        DB::table('copy_order')->where('id', $setUser->id)->update([
            'status' => ($request->action == 'active') ? 1 :0,
            'updated_at' => now()
        ]);
        

        return response()->json([
            'status' => 200,
            'message' => 'Update user follow successfully',
        ]);
    }

    public function postRemoveUser($user_follow) {
        $user_trader = DB::table('copy_trader')->where('userid', Auth::id())->where('status', 1)->first();
        $user_follow = DB::table('copy_order')->where('user_expert', $user_trader->userid)->where('user_follow', $user_follow)->where('status', 0)->first();
        if(is_null($user_follow)) {
            return response()->json([
                'status' => 422,
                'message' => 'User follow not found',
            ]);
        }
        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $user_follow->user_follow)->increment('live_balance', $user_follow->balance);
            DB::table('copy_order')->delete($user_follow->id);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Delete user follow successfully',
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Stop copy has an error.'
            ]);
        }
    }
}
