<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CommissionBonusController extends Controller
{
    public function getIndex() {
        $commissionbonus = DB::table('settings')->where('setting_name', 'bonus_commission_percent')->first();
        return view('admin.policy.commissionbonus.index', compact('commissionbonus'));
    }

    public function postIndex(Request $request) {
        $this->validate($request,
        [
            'value' => 'required|numeric'
        ]);
        $commissionbonus = DB::table('settings')->where('setting_name', $request->key)->first();
        if(is_null($commissionbonus)) {
            return response()->json([
                'error' => 1,
                'message' => 'The bouns commission the not exist.'
            ], 200);
        }
        DB::table('settings')->where('setting_name', $request->key)->update(['setting_value' => $request->value]);
        return response()->json([
                'error' => 0,
                'message' => 'The bouns commission update success.'
            ], 200);

    }
}
