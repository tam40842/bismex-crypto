<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Gate;
use Auth;

class HandController extends Controller
{
    public function index() {
        Gate::allows('modules', 'hand_access');

        $markets = DB::table('markets')->where('actived', 1)->get();
        $data = [
            'markets' => $markets
        ];
        return view('admin.hand.index', $data);
    }
    
    public function postIndex(Request $request) {
        Gate::allows('modules', 'hand_access');

        $this->validate($request, [
            'market_name' => 'required',
            'round_value' => 'required',
        ]);
        $market = DB::table('markets')->where('market_name', $request->market_name)->where('actived', 1)->first();
        if(is_null($market)) {
            return response()->json([
                'error' => 1,
                'message' => 'The market does not exist or not actived.'
            ]);
        }
        DB::table('markets')->where('market_name', $request->market_name)->update([
            'result' => $request->round_value
        ]);
        $candle = DB::table('tb_candle')->where('marketname', $request->market_name)->orderBy('id', 'desc')->first();
        DB::table('hand_histories')->insert([
            'marketname' => $request->market_name,
            'round_id' => $candle->time,
            'open' => $candle->open,
            'high' => $candle->high,
            'low' => $candle->low,
            'close' => $candle->close,
            'result' => $request->round_value,
            'author_id' => Auth::user()->id
        ]);
        // DB::table('tb_candle')->where('marketname', $request->market_name)->where('time', $round)->update([
        //     'adjust' => 1,
        //     'adjust_value' => $request->round_value,
        //     'adjust_userid' => Auth::user()->id,
        // ]);
        return response()->json([
            'error' => 0,
            'message' => 'Your setting has been approved.'
        ]);
    }

    public function getListOrder(Request $request) {
        Gate::allows('modules', 'hand_access');

        $orders = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.status', 0)->where('orders.type', 'live')->select('orders.*', 'users.username as username');
        if($request->has('market_name') && $request->market_name && strtoupper($request->market_name) != 'ALL') {
            $orders = $orders->where('market_name', $request->market_name);
        }
        if($request->has('sort_name') && $request->sort_name) {
            $orders = $orders->orderBy('orders.'.$request->sort_name, $request->sort_value);
        }
        $orders = $orders->get();
        $sell = 0;
        $buy = 0;
        foreach($orders as $key => $value) {
            if($value->action == 'SELL') {
                $sell += $value->amount;
            }
            if($value->action == 'BUY') {
                $buy += $value->amount;
            }
        }
        return response()->json([
            'error' => 0,
            'message' => 'Successfully',
            'data' => [
                'orders' => $orders,
                'sell' => $sell,
                'buy' => $buy
            ]
        ]);
    }
    public function getLastOrder(Request $request) {
        Gate::allows('modules', 'hand_access');
        
        $last_round = DB::table('orders')->where('status', '<>', 0)->orderBy('id', 'desc')->value('round');
        $orders = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where('orders.status', '<>', 0)->where('orders.type', 'live')->where('round', $last_round)->select('orders.*', 'users.username as username')->get();
        return response()->json([
            'error' => 0,
            'message' => 'Successfully',
            'data' => [
                'orders' => $orders,
            ]
        ]);
    }
}
