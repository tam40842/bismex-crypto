<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CommissionSale;
use App\CommissionLevel;

class CommissionSaleController extends Controller
{
    public function index() {
        $data = [
            'commissionsale' => CommissionSale::orderBy('id', 'desc')->paginate(100),
            'commission_sale_status' => $this->commission_sale_status(),
        ];
        return view('admin.policy.commissionsale.index', $data);
    }

    public function getAdd() {
        $commissionlevel = CommissionLevel::where('actived', 1)->get();
        return view('admin.policy.commissionsale.add', compact('commissionlevel'));
    }

    public function postAdd(Request $request) {
        $this->validate($request,
        [
            'level_name' => 'required|max:255',
            'floors' => 'required',
            'actived' => 'required|boolean',
        ]);
        CommissionSale::create([
            'level_name' => $request->level_name,
            'floors' => json_encode($request->floors),
            'actived' => intval($request->actived),
            'created_at' => date(now()),
            'updated_at' => date(now()),
        ]);

        return redirect()->back()->with('alert_success', 'Add sale commission successful.');
    }

    public function getEdit($id) {
        $commissionsale = CommissionSale::find($id);
        if(is_null($commissionsale)) {
            return abort('404');
        }
        return view('admin.policy.commissionsale.edit', compact('commissionsale'));
    }

    public function postEdit(Request $request, $id) {
        $commissionsale = CommissionSale::find($id);
        if(is_null($commissionsale)) {
            return abort('404');
        }
        $this->validate($request,
        [
            'level_name' => 'required|max:255',
            'floors' => 'required',
            'actived' => 'required|boolean',
        ]);
        $commissionsale->update([
            'level_name' => $request->level_name,
            'floors' => json_encode($request->floors),
            'actived' => intval($request->actived),
            'updated_at' => date(now()),
        ]);

        return redirect()->back()->with('alert_success', 'Update sale commission successful.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $commissionsale = CommissionSale::where(function($query) use ($search_text) {
            $query->where('level_name', 'LIKE', '%'.$search_text.'%');
        })->orderBy('id', 'desc')->paginate(100);
        
        $data = [
            'commissionsale' => $commissionsale,
            'commission_sale_status' => $this->commission_sale_status(), 
        ];

        return view('admin.policy.commissionsale._item', $data)->render();
    }
}
