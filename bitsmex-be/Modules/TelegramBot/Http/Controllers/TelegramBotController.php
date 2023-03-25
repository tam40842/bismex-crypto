<?php

namespace Modules\TelegramBot\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TelegramBotController extends Controller
{
    public function user()
    {
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
            )->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 200,
                'message' => 'User not exist or banned.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'SUCCESS',
            'user' => $user,
        ]);
    }

    public function getBlurs($market_name)
    {
        $market_name = strtoupper($market_name);
        $market = DB::table('markets')->where('market_name', $market_name)->where('actived', 1)->first();
        if (is_null($market)) {
            return response()->json([
                'status' => 422,
                'message' => 'The market does not exist.'
            ]);
        }
        $hour = Carbon::now()->setTimezone('UTC')->format('Y-m-d H');
        // $hour = Carbon::createFromFormat('Y-m-d H', $timestamp, 'Asia/Ho_Chi_Minh');
        // $hour->setTimezone('UTC');
        $round_result = DB::select('    SELECT ' .
            '        res.*' .
            '    FROM' .
            '        (SELECT ' .
            '            *' .
            '        FROM' .
            '            tb_candle' .
            '        INNER JOIN (SELECT ' .
            '            MAX(id) AS time_id,' .
            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i"),' .
            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i %s"),' .
            '                STR_TO_DATE(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s"),' .
            '                time AS time_val' .
            '        FROM' .
            '            tb_candle' .
            '        WHERE' .
            '            tb_candle.marketname = "' . $market->market_name . '"' .
            '        AND FROM_UNIXTIME(time, "%s") = "30" AND IF(CAST(DATE_FORMAT(NOW(), "%s") AS DECIMAL) BETWEEN 0 AND 30, STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "00"), "%Y-%M-%D %H %i %s"), STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "30"), "%Y-%M-%D %H %i %s"))' .
            '        GROUP BY time) AS b ON (tb_candle.id = b.time_id)' .
            '        ORDER BY tb_candle.id DESC' .
            '        LIMIT 100) AS res' .
            '    ORDER BY res.id');
        $results = [];
        foreach ($round_result as $key => $value) {
            $results[] = ($value->close < $value->open) ? 'CALL' : 'PUT';
        }

        return $results;
    }

    public function getBlursAll($market_name)
    {
        $market_name = strtoupper($market_name);
        $market = DB::table('markets')->where('market_name', $market_name)->where('actived', 1)->first();
        if (is_null($market)) {
            return response()->json([
                'status' => 422,
                'message' => 'The market does not exist.'
            ]);
        }

        $round_result = DB::select('    SELECT ' .
            '        res.*' .
            '    FROM' .
            '        (SELECT ' .
            '            *' .
            '        FROM' .
            '            tb_candle' .
            '        INNER JOIN (SELECT ' .
            '            MAX(id) AS time_id,' .
            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i"),' .
            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i %s"),' .
            '                STR_TO_DATE(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s"),' .
            '                time AS time_val' .
            '        FROM' .
            '            tb_candle' .
            '        WHERE' .
            '            tb_candle.marketname = "' . $market->market_name . '"' .
            '        AND IF(CAST(DATE_FORMAT(NOW(), "%s") AS DECIMAL) BETWEEN 0 AND 30, STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "00"), "%Y-%M-%D %H %i %s"), STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "30"), "%Y-%M-%D %H %i %s"))' .
            '        GROUP BY time) AS b ON (tb_candle.id = b.time_id)' .
            '        ORDER BY tb_candle.id DESC' .
            '        LIMIT 100) AS res' .
            '    ORDER BY res.id');
        $results = [];
        foreach ($round_result as $key => $value) {
            $results[] = ($value->close < $value->open) ? 'CALL' : 'PUT';
        }

        return $results;
    }

    public function getGeneral(Request $request)
    {
        $user  = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'User is banned.',
            ]);
        }

        $data = [];
        $range = null;
        switch ($request->range) {
            case 'yesterday':
                $range = Carbon::now()->subDay()->startOfDay();
                break;
            case 'today':
                $range = Carbon::now()->startOfDay();
                break;
            case 'month':
                $range = Carbon::now()->startOfMonth()->startOfDay();
                break;
            default:
                break;
        }

        // $data['withdraw'] = DB::table('withdraw')->where('userid', $user->id)->where('status', 1)->when($range, function ($query) use ($range) {
        //     return $query->where('created_at', '>=', $range);
        // })->sum('amount');
        // $data['deposit'] = DB::table('deposit')->where('userid', $user->id)->where('status', 1)->when($range, function ($query) use ($range) {
        //     return $query->where('created_at', '>=', $range);
        // })->sum('total');

        $data['orders'] = DB::table('orders')->where('orders.userid', $user->id)->leftJoin('users', 'users.id', '=', 'orders.userid')->when($range, function ($query) use ($range) {
            return $query->where('orders.created_at', '>=', $range);
        })->where('orders.status', '<>', 0)->where('orders.type', 'live')->select(
            DB::raw('COUNT(orders.id) as total_orders, 
                    SUM(case when orders.status = 1 then orders.amount - (orders.amount * orders.profit_percent/100) else 0 end) as total_fee, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else 0 end) as total_win, 
                    SUM(case when orders.status = 2 then orders.amount else 0 end) as total_lose, 
                    SUM(case when orders.status = 1 then orders.amount * orders.profit_percent/100 else orders.amount end) as total_volume')
        )->first();

        $data['balance'] = $user->live_balance;

        return response()->json([
            'status' => 200,
            'message' => 'SUCCESS',
            'data' => $data,
        ]);
    }

    public function getOrderStatus(Request $request)
    {
        $user  = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'User is banned.',
            ]);
        }

        $order = DB::table('orders')->where('userid', $user->id)->where('orderid', $request->orderid)->first();
        return response()->json([
            'status' => 200,
            'message' => 'SUCCESS',
            'data' => [
                'order' => $order,
                'balance' => $user->live_balance
            ],
        ]);
    }
}
