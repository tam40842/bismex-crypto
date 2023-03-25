<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use DB;

class ApiController extends Controller
{
    public function getMarkets() {
        $markets = DB::table('markets')->where('actived', 1)->select('market_name', 'result', 'type', 'random')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'data' => [
                'markets' => $markets,
            ]
        ], 200);
    }
	
	public function getFee() {
        $fee = DB::table('trade_fee')->pluck('value', 'hour');
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'data' => $fee
        ], 200);
    }

    

    public function getWalletAddress(Request $request) {
        $address = DB::table('wallet_address')->select('input_address as address')->get();
        foreach($address as $key => $value) {
            $address[$key]->mainnet = 'BSC';
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get all data',
            'results' => $address
        ]);
    }
}
