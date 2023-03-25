<?php

namespace Modules\Trading\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\Vuta\Vuta;
use App\TransactionHistory;
use DB;
use Modules\Trading\Entities\Order;
use Modules\AntiCheat\Entities\Anti;

class TradingController extends Controller
{
    public $playmode;

    public function __construct()
    {
        $this->playmode = ['live', 'demo'];
        $this->action = ['BUY', 'SELL'];
        $this->mode = ['basic', 'advance'];
    }

    public function changePlayMode(Request $request)
    {
        $validator = Validator::make($request->all(), ['playmode' => 'required|string']);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        if (!in_array($request->playmode, $this->playmode)) {
            return response()->json([
                'status' => 422,
                'message' => 'The play mode is not valid.'
            ]);
        }
        $user = Auth::user();
        $user->play_mode = $request->playmode;
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Your play mode has been saved.'
        ]);
    }

    public function Placed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'market_name' => 'required|string',
            'action' => 'required|string',
            'mode' => 'required|string',
            'expired_at' => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        if (!in_array($request->mode, $this->mode)) {
            return response()->json([
                'status' => 422,
                'message' => 'The Chart mode is not valid.'
            ]);
        }

        $action = strtoupper($request->action);
        if (!in_array($request->action, $this->action)) {
            return response()->json([
                'status' => 422,
                'message' => 'The action is not valid.'
            ]);
        }
        
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)
                ->select(
                    'id',
                    'sponsor_id',
                    'sponsor_code',
                    'sponsor_ref',
                    'sponsor_level',
                    'status',
                    'live_balance',
                    'demo_balance',
                    'primary_balance',
                    'play_mode',
                    'markets',
                    'market_active',
                    'active_commission',
                    'admin_setup',
                    'created_at',
                    'updated_at'
                )->lockForUpdate()->first();

            $balance = 0;
            $playMode = $request->playmode;
            if (is_null($playMode)) {
                $playMode = $user->play_mode;
            } else {
                if (!in_array($request->playmode, $this->playmode)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'The play mode is not valid.'
                    ]);
                }
            }

            switch ($playMode) {
                case 'demo':
                    $balance = $user->demo_balance;
                    break;
                case 'live':
                    $balance = $user->live_balance;
                    break;
            }
            $amount = abs((float)$request->amount);
            if ($amount > $balance) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your balance is not enough.'
                ]);
            }

            $settings = Vuta::get_settings(['trade_range']);
            $trade_range = explode(';', $settings['trade_range']);
            if ($amount < $trade_range[0]) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Trade min is ' . number_format($trade_range[0], 2) . ' USD'
                ]);
            }
            if ($amount > $trade_range[1]) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Trade max is ' . number_format($trade_range[1], 2) . ' USD'
                ]);
            }
            $market_exist = DB::table('markets')->where('market_name', $request->market_name)->where('actived', 1)->exists();
            if (!$market_exist) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The market does not exist.'
                ]);
            }
            $hour_profit = DB::table('trade_fee')->where('hour', date('H'))->value('value');
            $candle = DB::table('tb_candle')->where('marketname', $request->market_name)->orderBy('id', 'desc')->select('time', 'close', 'open')->first();
            //Ngoc update open_price 04/11/2020
            $data = [
                'orderid' => Vuta::random_code(),
                'action' => $action,
                'market_name' => $request->market_name,
                'userid' => $user->id,
                'round' => $candle->time,
                'amount' => $amount,
                'profit_percent' => $hour_profit,
                'type' => $playMode,
                'chartmode' => $request->mode,
                'admin_setup' => $user->admin_setup,
                'open_price' => $candle->open,
                // 'close_price' => $candle->close,
                'created_at' => date(now()),
                'updated_at' => date(now()),
            ];

            $seconds = Carbon::now()->second;
            $minutes = Carbon::now()->minute;
            switch ($request->mode) {
                case 'basic':
                    if ($seconds > 29) {
                        return response()->json([
                            'status' => 422,
                            'message' => 'Time out'
                        ]);
                    }
                    $data['expired'] = 1;
                    $data['expired_at'] = Carbon::now()->addMinute(1)->format('Y-m-d H:i');
                    break;
                case 'advance':
                    if ($request->expired_at == 1) {
                        if ($seconds > 29) {
                            return response()->json([
                                'status' => 422,
                                'message' => 'The expired time is short, please try again.'
                            ]);
                        }
                    }
                    $data['expired'] = intval($request->expired_at);
                    $data['expired_at'] = Carbon::now()->addMinute($data['expired'])->format('Y-m-d H:i');
                    break;
                default:
                    return response()->json([
                        'status' => 422,
                        'message' => 'Access denied.'
                    ]);
                    break;
            }


            DB::table('orders')->insert($data);
            if($playMode == 'live'){
                TransactionHistory::historyLiveBalance($user->id, 'PLACED', $amount*-1, 'live_balance', $data['orderid']);
            }
            DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement($playMode . '_balance', $amount);
            DB::commit();
            Anti::firewall($user->id, 'trade', [
                'user' => $user,
                'amount' => $amount,
                'created_at' => date(now())
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Your order was placed successfully',
                'shape' => [
                    'orderid' => $data['orderid'],
                    'action' => $action,
                    'profit_percent' => $hour_profit,
                    'mode' => $request->mode,
                    'time' => $candle->time,
                    'price' => $candle->close,
                    'amount' => $amount
                ],
                'live_balance' => $user->live_balance,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Place has error',
            ]);
        }
        return response()->json([
            'status' => 422,
            'message' => 'Access denied.'
        ]);
    }

    public function addShapeId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderid' => 'required|string',
            'shapeid' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $order = DB::table('orders')->where('orderid', $request->orderid)->where('userid', $user->id)->first();
        if (is_null($order)) {
            return response()->json([
                'status' => 422,
                'message' => 'The order does not exist.'
            ]);
        }
        DB::table('orders')->where('orderid', $request->orderid)->update([
            'shapeid' => $request->shapeid
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'The shape possition has been updated.'
        ]);
    }

    public function getPendingOrder()
    {
        $user = Auth::user();
        $orders = DB::table('orders')->where('status', 0)->where('userid', $user->id)->select('action', 'amount')->get();
        $total = [
            'BUY' => 0,
            'SELL' => 0
        ];
        foreach ($orders as $key => $value) {
            $total[$value->action] += $value->amount;
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => $total
        ]);
    }
    public function getResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderid' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $orders = DB::table('orders')->where('orderid', $request->orderid)->first();
        if (is_null($orders)) {
            return response()->json([
                'message' => 'Order does not exist!'
            ], 404);
        }
        if ($orders->status == 0) {
            $status = 'PENDING';
        } else if ($orders->status == 1) {
            $status = 'WIN';
        } else {
            $status = 'LOSE';
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => $status
        ]);
    }
}
