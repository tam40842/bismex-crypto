<?php

namespace Modules\Market\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use DB;
use Carbon\Carbon;
use Auth;
use Validator;

class MarketController extends Controller
{
    public function getBlurs($market_name) {
        $market_name = strtoupper($market_name);
        $market = DB::table('markets')->where('market_name', $market_name)->where('actived', 1)->first();
        if(is_null($market)) {
            return response()->json([
                'status' => 422,
                'message' => 'The market does not exist.'
            ]);
        }
        $hour =Carbon::now()->format('Y-m-d H');
        // $hour = Carbon::createFromFormat('Y-m-d H', $timestamp, 'Asia/Ho_Chi_Minh');
        // $hour->setTimezone('UTC');
        $round_result = DB::select("SELECT 
                                        *
                                    FROM
                                        tb_candle
                                            INNER JOIN
                                        (SELECT 
                                            MAX(id) AS id, FROM_UNIXTIME(time, '%Y-%m-%d %H %i'), FROM_UNIXTIME(time, '%Y-%m-%d %H %i %s'), STR_TO_DATE(DATE_FORMAT(now(),'%Y-%M-%D %H %i %s'),'%Y-%M-%D %H %i %s'), time
                                        FROM
                                            tb_candle
                                        WHERE
                                            tb_candle.marketname = '".$market->market_name."'
                                                AND FROM_UNIXTIME(time, '%Y-%m-%d %H') = '".$hour."'
                                                AND IF(CAST(DATE_FORMAT(now(),'%s') AS DECIMAL) BETWEEN 0 AND 30,FROM_UNIXTIME(time, '%i') < MINUTE(now()),
                                                            STR_TO_DATE(FROM_UNIXTIME(time, '%Y-%M-%D %H %i %s'),'%Y-%M-%D %H %i %s') < STR_TO_DATE(CONCAT(DATE_FORMAT(now(),'%Y-%M-%D %H %i '),'30'),'%Y-%M-%D %H %i %s'))
                                        GROUP BY time) AS b ON (tb_candle.id = b.id)");
        $results = [];
        foreach($round_result as $key => $value) {
            $results[] = ($value->close < $value->open) ? 'CALL' : 'PUT';
        }
        $user = Auth::user();
        DB::table('users')->where('id', $user->id)->update([
            'market_active' => $market_name
        ]);
        return $results;
    }

    public function getMarkets() {
        $markets = DB::table('markets')->where('actived', 1)->orderBy('type', 'asc')->get();
        $market_active = Auth::check() ? Auth::user()->market_active : 'BTCUSDT';
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'data' => [
                'markets' => $markets,
                'market_active' => DB::table('markets')->where('market_name', $market_active)->where('actived', 1)->first()
            ]
        ]);
    }
    
    public function saveMarket(Request $request) {
        $validator = Validator::make($request->all(), [
            'marketname' => 'required|string'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $market_active = strtoupper($request->marketname);
        $market = DB::table('markets')->where('market_name', $market_active)->where('actived', 1)->first();
        if(is_null($market)) {
            return response()->json([
                'status' => 422,
                'message' => 'The market does not exist.'
            ]);
        }
        $user = Auth::user();
        DB::table('users')->where('id', $user->id)->update([
            'market_active' => $market_active
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'The market has been changed.',
            'data' => [
                'market' => $market
            ]
        ]);
    }
}
