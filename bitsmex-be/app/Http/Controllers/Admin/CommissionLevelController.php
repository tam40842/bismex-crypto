<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CommissionLevel;

class CommissionLevelController extends Controller
{
    public function index() {
        $commissionlevel = CommissionLevel::orderBy('id', 'desc')->paginate(100);
        $data = [
            'commissionlevel' => $commissionlevel,
            'commission_level_status' => $this->commission_level_status(), 
        ];
        return view('admin.policy.commissionlevel.index', $data);
    }

    public function getAdd() {
        return view('admin.policy.commissionlevel.add');
    }

    public function postAdd(Request $request) {
        $this->validate($request,
        [
            'level_name' => 'required|max:255|unique:commission_levels,level_name',
            'level_number' => 'required|integer|unique:commission_levels,level_number',
            'percent' => 'required|numeric',
            'f1_count' => 'required|integer|max:255',
            'personal_volume' => 'required|numeric',
            'f1_volume' => 'required|numeric',
            'actived' => 'required|boolean'
        ]);
        CommissionLevel::create([
            'level_name' => $request->level_name,
            'level_number' => $request->level_number,
            'percent' => $request->percent,
            'f1_count' => $request->f1_count,
            'personal_volume' => $request->personal_volume,
            'f1_volume' => $request->f1_volume,
            'actived' => intval($request->actived),
            'created_at' => date(now()),
            'updated_at' => date(now()),
        ]);

        return redirect()->route('admin.policy.commissionlevel')->with('alert_success', 'Thêm hoa hồng thành công.');
    }

    public function getEdit($id) {
        $commissionlevel = CommissionLevel::find($id);
        if(is_null($commissionlevel)) {
            return abort('404');
        }
        return view('admin.policy.commissionlevel.edit', compact('commissionlevel'));
    }

    public function postEdit(Request $request, $id) {
        $commissionlevel = CommissionLevel::find($id);
        if(is_null($commissionlevel)) {
            return abort('404');
        }
        $this->validate($request,
        [
            'level_name' => 'required|max:255|unique:commission_levels,level_name,'.$commissionlevel->id,
            'level_number' => 'required|integer|unique:commission_levels,level_number,'.$commissionlevel->id,
            'percent' => 'required|numeric',
            'f1_count' => 'required|integer|max:255',
            'personal_volume' => 'required|numeric',
            'f1_volume' => 'required|numeric',
            'actived' => 'required|boolean',
        ]);
        $commissionlevel->update([
            'level_name' => $request->level_name,
            'level_number' => $request->level_number,
            'percent' => $request->percent,
            'f1_count' => $request->f1_count,
            'personal_volume' => $request->personal_volume,
            'f1_volume' => $request->f1_volume,
            'actived' => intval($request->actived),
            'updated_at' => date(now()),
        ]);

        return redirect()->back()->with('alert_success', 'Cập nhật hoa hồng thành công.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $commissionlevel = CommissionLevel::where(function($query) use ($search_text) {
            $query->where('level_name', 'LIKE', '%'.$search_text.'%')
            ->orwhere('level_number', 'LIKE', '%'.$search_text.'%');
        })->orderBy('id', 'desc')->paginate(100);
        
        $data = [
            'commissionlevel' => $commissionlevel,
            'commission_level_status' => $this->commission_level_status(), 
        ];
        return view('admin.policy.commissionlevel._item', $data)->render();
    }
}
