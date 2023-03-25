<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Gate;

class TradeFeeController extends Controller
{
    public function index() {
        Gate::allows('modules', 'settings_tradefee_access');

        $tradefee = DB::table('trade_fee')->get();
        $settings = $this->get_settings(['system_win_percent','autotrade_percent','transfer_limit', 'trade_range', 'withdraw_fee']);

        $transfer_limit = $settings['transfer_limit'];
        $transfer_limit = explode(';', $transfer_limit);
        $transfer_min = $transfer_limit[0];
        $transfer_max = $transfer_limit[1];

        $trade_range = $settings['trade_range'];
        $trade_range = explode(';', $trade_range);
        $trade_min = $trade_range[0];
        $trade_max = $trade_range[1];
        $data = [
            'tradefee' => $tradefee,
            'system_win_percent' => $settings['system_win_percent'],
            'autotrade_percent' => $settings['autotrade_percent'],
            'trade_range' => $settings['trade_range'],
            'withdraw_fee' => $settings['withdraw_fee'],
            'transfer_min' => $transfer_min,
            'transfer_max' => $transfer_max,
            'trade_min' => $trade_min,
            'trade_max' => $trade_max,
        ];
        
        return view('admin.tradefee.index', $data);
    }
    public function postIndex(Request $request) {
        Gate::allows('modules', 'settings_tradefee_access');

        if(in_array($request->key, ['system_win_percent','autotrade_percent', 'withdraw_fee', 'transfer_min', 'transfer_limit'])) {
            $this->validate($request,
            [
                'value' => 'required|integer'
            ]);
            $setting_fee = DB::table('settings')->where('setting_name', $request->key)->first();
            if(is_null($setting_fee)) {
                return response()->json([
                        'error' => 1,
                        'message' => $request->key . 'not found.',
                    ], 200);
            }
            DB::table('settings')->where('setting_name', $request->key)->update(['setting_value' => $request->value]);

            return response()->json([
                'error' => 0,
                'message' => $request->key . ' has been updated.',
            ], 200);
        }
        $this->validate($request,
        [
            'key' => 'required|integer',
            'value' => 'required|integer',
        ]);
        $tradefee = DB::table('trade_fee')->where('hour', $request->key)->first();
        if(is_null($tradefee)) {
            return response()->json([
                'error' => 1,
                'message' => 'The hour does not exist.',
            ], 200);
        }
        DB::table('trade_fee')->where('hour', $request->key)->update([
            'value' => $request->value
        ]);
        return response()->json([
            'error' => 0,
            'message' => 'The fee has been updated.',
        ], 200);
    }

    public function postRange(Request $request) {
        Gate::allows('modules', 'settings_tradefee_access');
        
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required',
        ]);
        DB::table('settings')->where('setting_name', $request->key)->update([
            'setting_value' => $request->value
        ]);
        return response()->json([
            'error' => 0,
            'message' => 'The trade range has been updated.',
        ], 200);
    }
}
