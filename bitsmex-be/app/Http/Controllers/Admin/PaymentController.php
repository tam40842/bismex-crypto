<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Payments;

class PaymentController extends Controller
{
    public function index() {
        $data = [
            'payments' => Payments::orderBy('id', 'desc')->paginate(10)
        ];
        return view('admin.payment.index', $data);
    }

    public function getEdit($id) {
        $payment = Payments::find($id);
        if(is_null($payment)) {
            return redirect()->route('admin.payments')->with('alert_error', 'Payment method does not exists.');
        }
        $payment->login_id = decrypt($payment->login_id);
        $payment->login_password = decrypt($payment->login_password);
        return view('admin.payment.edit', ['payment' => $payment]);
    }

    public function postEdit(Request $request, $id) {
        $payment = Payments::find($id);
        if(is_null($payment)) {
            return redirect()->back()->with('alert_error', 'Payment method does not exists.');
        }
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'required|url',
            'type' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'account_branch' => 'required|string',
            'login_id' => 'required|string',
            'login_password' => 'required|string',
            'deposit_fee' => 'required|min:0',
            'withdraw_fee' => 'required|min:0',
            'auto_balance' => 'required|boolean',
            'actived' => 'required|boolean',
        ]);
        
        $payment->update([
            'name' => $request->name,
            'logo' => $request->logo,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'account_branch' => $request->account_branch,
            'deposit_fee' => $request->deposit_fee,
            'withdraw_fee' => $request->withdraw_fee,
            'auto_balance' => intval($request->auto_balance),
            'actived' => intval($request->actived),
            'login_id' => encrypt($request->login_id),
            'login_password' => encrypt($request->login_password),
        ]);
        return redirect()->route('admin.payments')->with('alert_success', 'Payment method updated successfully.');
    }
}
