<?php

namespace Modules\History\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use DB;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Date;
use Validator;
use App\Http\Controllers\Vuta\Status;
use Cache;

class HistoryController extends Controller
{
    use Status;

    public $type = ['live', 'demo'];

    public function getStastics(Request $request, $type) {
        if(!in_array($type, $this->type)) {
            return response()->json([
                'status' => 422,
                'message' => 'The history type is not valid.'
            ]);
        }
        $user = Auth::user();
        $type = strtolower($type);
        $histories = DB::table('orders')->where('type', $type)->where('userid', $user->id)->where('status', '!=', 0);
        if(isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from)->startOfDay();
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to)->endOfDay() : Carbon::now()->endOfDay();
            $histories = $histories->where('created_at', '>=', $date_start)->where('created_at', '<=', $date_end);
	}
	//else {
           // $histories = $histories->whereDate('created_at', now());
        //}
        if(isset($request->market)) {
            $histories = $histories->where('market_name', $request->market);
        }
        $histories = $histories->select(DB::raw('count(case when status = 2 then id end) as lose_count, count(case when status = 1 then id end) as win_count, IFNULL(sum(amount), 0) as volume, IFNULL(sum(case when status = 2 then amount else 0 end), 0) as lose_total, IFNULL(sum(case when status = 1 then amount * profit_percent / 100 else 0 end), 0) as win_total'))->get();
        $histories[0]->profit_total = ($histories[0]->win_total-$histories[0]->lose_total);
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => isset($histories[0]) ? $histories[0] : []
        ]);
    }

    public function HistoriesTime($type) {
        $user = Auth::user();
        $histories_orders = DB::table('orders')->where('userid', $user->id)->where('type', 'live');
        $histories_deposit = DB::table('deposit')->where('userid', $user->id)->where('status', 1);
        $histories_withdraw = DB::table('withdraw')->where('userid', $user->id)->where('status', 1);
        $waiting = DB::table('orders')->where('userid', $user->id)->where('type', 'live');
        switch ($type) {
            case 'today':
                $waiting = $waiting->whereDate('created_at', now())->select(DB::raw('
                    SUM(case when status = 1 then amount - (amount * profit_percent/100) else 0 end) as total_fee,
                    SUM(case when status = 1 then amount*profit_percent/100 else 0 end) as total_win, 
                    SUM(case when status = 2 then amount else 0 end) as total_lose,
                    SUM(case when status = 0 then amount else 0 end) as total_waiting,
                    SUM(amount) as total_amount'))->first();
                // $begin = DB::table('orders')->where('userid', $user->id)->where('type', 'live')->where('created_at', '<', Carbon::now()->startOfDay())->sum('amount');
                $histories_orders = $histories_orders->where('status', '<>', 0)->whereDate('created_at', now())->orderBy('id', 'desc')->get();
                $histories_deposit = $histories_deposit->whereDate('created_at', now())->sum('total');
                $histories_withdraw = $histories_withdraw->whereDate('created_at', now())->sum('total');
                break;
            
            case 'month':
                $waiting = $waiting->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->select(DB::raw('
                    SUM(case when status = 1 then amount - (amount * profit_percent/100) else 0 end) as total_fee,
                    SUM(case when status = 1 then amount*profit_percent/100 else 0 end) as total_win, 
                    SUM(case when status = 2 then amount else 0 end) as total_lose,
                    SUM(case when status = 0 then amount else 0 end) as total_waiting,
                    SUM(amount) as total_amount'))->first();
                // $begin = DB::table('orders')->where('userid', $user->id)->where('type', 'live')->where('created_at', '<', Carbon::now()->startOfMonth())->sum('amount');
                $histories_orders = $histories_orders->where('status', '<>', 0)->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->orderBy('id', 'desc')->get();
                $histories_deposit = $histories_deposit->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->sum('total');
                $histories_withdraw = $histories_withdraw->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->sum('total');
                break;
            
            default:
                $waiting = $waiting->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->select(DB::raw('
                    SUM(case when status = 1 then amount - (amount * profit_percent/100) else 0 end) as total_fee,
                    SUM(case when status = 1 then amount*profit_percent/100 else 0 end) as total_win, 
                    SUM(case when status = 2 then amount else 0 end) as total_lose,
                    SUM(case when status = 0 then amount else 0 end) as total_waiting,
                    SUM(amount) as total_amount'))->first();
                // $begin = $waiting->total_amount;  
                $histories_orders = $histories_orders->where('status', '<>', 0)->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->orderBy('id', 'desc')->limit(100)->get();
                $histories_deposit = $histories_deposit->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->sum('total');
                $histories_withdraw = $histories_withdraw->whereBetWeen('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->sum('total');
                break;
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'histories_orders' => $histories_orders,
                'histories_deposit' => $histories_deposit,
                'histories_withdraw' => $histories_withdraw,
                // 'waiting' => $waiting->total_waiting,
                'profit' => $waiting->total_win - $waiting->total_lose,
                // 'begin' => $begin,
            ]
        ]);
    }

    public function getBotOrders() {
        return response()->json([
            'status' => 200,
            'data' => Cache::get('bot-orders'),
        ]);
    }
    
    // public function getStastics(Request $request) {
    //     $user = Auth::user();
    //     $today = Carbon::now()->format('Y-m-d');
    //     $now = Carbon::now();
    //     $orders = DB::table('orders')->where('userid', $user->id);
    //     // if($request->has('date_from') && $request->date_from != '' && $request->has('date_to') && $request->date_to != '') {
    //     //     $orders = $orders->whereBetween('created_at', [$request->date_from, $request->date_to]);
    //     // } else {
    //     //     $orders = $orders->whereBetween('created_at', [$today, $now]);
    //     // }
    //     // if($request->has('market') && $request->market != '') {
    //     //     $orders = $orders->where('market_name', $request->market);
    //     // }
    //     $orders = $orders->get();
    //     $stastics = [];
    //     foreach($orders as $key => $value) {
    //         $stastics[$value->type]['amount'][] = $value->amount;
    //         $stastics[$value->type][$value->status][] = $value->amount;
    //     }
    //     $_stastics = [];
    //     foreach($stastics as $key => $value) {
    //         $_stastics[$key] = [
    //             'total' => array_sum($stastics[$key]['amount']),
    //             'win' => isset($stastics[$key][1]) ? array_sum($stastics[$key][1]) : 0,
    //             'lose' => isset($stastics[$key][2]) ? array_sum($stastics[$key][2]) : 0,
    //             'win_count' => isset($stastics[$key][1]) ? count($stastics[$key][1]) : 0,
    //             'lose_count' => isset($stastics[$key][2]) ? count($stastics[$key][2]) : 0
    //         ];
    //     }
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Success',
    //         'data' => $_stastics
    //     ]);
    // }

    public function getHistories(Request $request, $type) {
        if(!in_array($type, $this->type)) {
            return response()->json([
                'status' => 422,
                'message' => 'The history type is not valid.'
            ]);
        }
        $user = Auth::user();
        $type = strtolower($type);
        $histories = DB::table('orders')->where('type', $type)->where('userid', $user->id);
        if(isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from);
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to) : Carbon::now();
            $histories = $histories->whereDate('created_at', '>=', $date_start)->whereDate('created_at', '<=', $date_end);
        }
        if(isset($request->market)) {
            $histories = $histories->where('market_name', $request->market);
        }
        $histories = $histories->orderBy('id', 'desc')->paginate(10);
        $status = $this->order_status();

        $stastics = [];
        foreach($histories as $key => $value) {
            $stastics[$value->type]['amount'][] = $value->amount;
            $stastics[$value->type][$value->status][] = $value->amount;
        }
        $_stastics = [];
        foreach($stastics as $key => $value) {
            $_stastics[$key] = [
                'total' => array_sum($stastics[$key]['amount']),
                'win' => isset($stastics[$key][1]) ? array_sum($stastics[$key][1]) : 0,
                'lose' => isset($stastics[$key][2]) ? array_sum($stastics[$key][2]) : 0,
                'win_count' => isset($stastics[$key][1]) ? count($stastics[$key][1]) : 0,
                'lose_count' => isset($stastics[$key][2]) ? count($stastics[$key][2]) : 0
            ];
        }
        foreach($histories as $key => $value) {
            $histories[$key] = $value;
            $histories[$key]->action = $value->action == 'BUY' ? 'Higher' : 'Lower';
            $histories[$key]->status_html = $status[$value->status];
            $histories[$key]->time_frame = Carbon::create($value->created_at)->diffInSeconds($value->expired_at);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Get data success',
            'data' => [
                'histories' => $histories,
                'stastics' => $_stastics
            ]
        ]);
    }

    public function getHistoriesWeek() {
        $user = Auth::user();
        $histories = DB::table('orders')->where('userid', $user->id)->where('type', 'live')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 200,
            'data' => $histories
        ]);
    }
}
