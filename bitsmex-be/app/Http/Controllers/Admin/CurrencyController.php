<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Gate;
use DB;

class CurrencyController extends Controller
{
    public function index() {
        Gate::allows('modules', 'currencies_access');

        $data = [
            'currencies' => DB::table('currencies')->orderBy('sort', 'asc')->paginate('10')
        ];
        return view('admin.currencies.index', $data);
    }

    public function getEdit($id) {
        Gate::allows('modules', 'currencies_edit');

        $currency = DB::table('currencies')->where('id', $id)->first();
        if(is_null($currency)) {
            return redirect()->route('admin.currencies')->with('alert_error', 'The currency does not exist.');
        }
        
        $data['currency'] = $currency;
        return view('admin.currencies.edit', $data);
    }

    public function postEdit(Request $request, $id) {
        Gate::allows('modules', 'currencies_edit');

        $this->validate($request, [
            'logo' => 'required',
            // 'balance' => 'required|numeric|min:0',
            'auto_balance' => 'boolean',
            'actived' => 'boolean',
            'is_default' => 'boolean',
            'deposit_fee' => 'required|min:0|numeric',
            'deposit_min' => 'required|min:0|numeric',
            'withdraw_min' => 'required|min:0|numeric',
            'withdraw_max' => 'required|min:0|numeric',
            'withdraw_fee' => 'required|min:0|numeric',
        ]);

        $currency = DB::table('currencies')->where('id', $id);
        if(is_null($currency->first())) {
            return redirect()->route('admin.currencies')->with('alert_error', 'The currency does not exist.');
        }
        $currency->update([
            'logo' => $request->logo,
            // 'balance' => $request->balance,
            // 'auto_balance' => intval($request->auto_balance),
            'actived' => intval($request->actived),
            'deposit_fee' => $request->deposit_fee,
            'deposit_min' => $request->deposit_min,
            'withdraw_fee' => $request->withdraw_fee,
            'withdraw_min' => $request->withdraw_min,
            'withdraw_max' => $request->withdraw_max,
        ]);
        return redirect()->route('admin.currencies')->with('alert_success', 'The currency has been update successfully.');
    }

    public function postSearch(Request $request) {
        $search_text = $request->search_text;
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $currencies = Currencies::where(function($query) use ($search_text) {
            $query->where('name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('symbol', 'LIKE', '%'.$search_text.'%')
            ->orWhere('balance', 'LIKE', '%'.$search_text.'%')
            ->orWhere('deposit_fee', 'LIKE', '%'.$search_text.'%')
            ->orWhere('withdraw_fee', 'LIKE', '%'.$search_text.'%');
        })->orderBY('id', 'desc')->paginate(10);
        
        $data = [
            'currencies' => $currencies,
        ];
        return view('admin.currencies._item', $data)->render();
    }
}
