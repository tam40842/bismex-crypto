<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Markets;
use DB;
use Gate;

class MarketController extends Controller
{
    public function getIndex() {
        Gate::allows('modules', 'market_access');

        $market = DB::table('markets')->get();
        foreach($market as $key => $value) {
            $data[$value->type][] = $value;
        }
        return view('admin.market.index', compact('data'));
    }

    public function postIndex(Request $request) {
        Gate::allows('modules', 'market_access');

        $this->validate($request,
        [
            'key' => 'required',
            'value' => 'required|boolean',
        ]);
        $market_ = DB::table('markets')->select('market_name')->get();
        $market = [];
        foreach($market_ as $key => $value) {
            $market[$key] = $value->market_name;
        }
        if(!in_array($request->key, $market)) {
            return response()->json([
                'error' => 1,
                'message' => 'Market does not exist.'
            ], 200);
        }
        DB::table('markets')->where('market_name', $request->key)->update([
            'actived' => $request->value,
            'updated_at' => date(now()),
        ]);

        return response()->json([
            'error' => 0,
            'message' => 'Market has been updated.',
        ], 200);
    }
}
