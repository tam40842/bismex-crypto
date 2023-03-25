<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Gate;

class CopytradingController extends Controller
{
    public function index() {
        Gate::allows('modules', 'copytrading_access');

        $supperTrade = DB::table('copy_trader')->paginate(10);
        $data = [
            'supperTrade' => $supperTrade,
            'supper_trader_status' => $this->supper_trader_status(),
        ];

        return view('admin.copytrading.index', $data);
    }

    public function getAdd() {
        Gate::allows('modules', 'copytrading_add');

        $data = [
            'action' => 'add'
        ];

        return view('admin.copytrading.super_trader', $data);
    }

    public function postAdd(Request $request) {
        Gate::allows('modules', 'copytrading_add');

        $this->validate($request, [
            'email' => 'required|email|unique:copy_trader,email',
            'name' => 'required|unique:copy_trader,name',
            'amount_min' => 'required|numeric|min:1',
            'fee' => 'required|numeric|min:0',
            'profit' => 'required|numeric|min:0'
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();
        if(is_null($user)) {
            return redirect()->route('admin.copytrading')->with('alert_error', 'User not found');
        }

        DB::table('copy_trader')->insert([
            'userid' => $user->id,
            'email' => $request->email,
            'name' => $request->name,
            'amount_min' => $request->amount_min,
            'fee' => $request->fee,
            'profit' => $request->profit,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.copytrading')->with('alert_success', 'Add super trader successfully');
    }

    public function getEdit($id) {
        Gate::allows('modules', 'copytrading_edit');

        $superTrade = DB::table('copy_trader')->where('id', $id)->first();
        if(is_null($superTrade)) {
            return redirect()->route('admin.copytrading')->with('alert_error', 'Super trader not found');
        }
        
        $data = [
            'action' => 'edit',
            'superTrade' => $superTrade,
        ];
        
        return view('admin.copytrading.super_trader', $data);
    }

    public function postEdit(Request $request, $id) {
        Gate::allows('modules', 'copytrading_edit');

        $superTrade = DB::table('copy_trader')->where('id', $id)->first();
        if(is_null($superTrade)) {
            return redirect()->route('admin.copytrading')->with('alert_error', 'Super trader not found');
        }

        $this->validate($request, [
            'email' => 'required|email|unique:copy_trader,email,'.$superTrade->id,
            'name' => 'required|unique:copy_trader,name,'.$superTrade->id,
            'amount_min' => 'required|numeric',
            'fee' => 'required|numeric',
            'profit' => 'required|numeric',
            'status' => 'required|integer'
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();
        if(is_null($user)) {
            return redirect()->route('admin.copytrading')->with('alert_error', 'User not found');
        }

        DB::table('copy_trader')->where('id', $superTrade->id)->update([
            'userid' => $user->id,
            'email' => $request->email,
            'name' => $request->name,
            'amount_min' => $request->amount_min,
            'fee' => $request->fee,
            'profit' => $request->profit,
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.copytrading')->with('alert_success', 'Update super trader successfully');
    }
}
