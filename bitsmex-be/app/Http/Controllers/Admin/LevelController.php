<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use App\Level;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index(Request $request) {
        $data = [
            'levels' => Level::orderBy('id', 'asc')->paginate(10),
        ];
        return view('admin.users.levels.index', $data);
    }
    
    public function getAdd() {
        return view('admin.users.levels.add');
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'level_name' => 'required|unique:levels',
            'level' => 'required|unique:levels|min:0|numeric',
            'percent' => 'required|min:0|numeric',
        ]);
        Level::create([
            'level_name' => $request->level_name,
            'level' => $request->level,
            'percent' => $request->percent,
        ]);
        return redirect()->route('admin.levels')->with('alert_success', 'Tạo robot trade thành công.');
    }

    public function getEdit($id) {
        $level = Level::find($id);
        if(is_null($level)) {
            return redirect()->back()->with('alert_error', 'The level does not exist.');
        }
        $data = [
            'level' => $level
        ];
        return view('admin.users.levels.add', $data);
    }

    public function postEdit(Request $request, $id) {
        $level = Level::find($id);
        if(is_null($level)) {
            return redirect()->back()->with('alert_error', 'The level does not exist.');
        }
        $this->validate($request, [
            'level_name' => ['required', Rule::unique('levels')->ignore($id)],
            'level' => ['required', 'min:0', 'numeric', Rule::unique('levels')->ignore($id)],
            'percent' => 'required|min:0|numeric',
        ]);
        $level->update([
            'level_name' => $request->level_name,
            'level' => $request->level,
            'percent' => $request->percent,
        ]);
        
        return redirect()->route('admin.levels')->with('alert_success', 'Cập nhật level thành công.');
    }

    public function getDelete($id) {
        $level = Level::find($id);
        if(is_null($level)) {
            return redirect()->back()->with('alert_error', 'Level does not exist.');
        }
        $level->delete();
        return redirect()->route('admin.levels')->with('alert_success', 'Xóa level thành công.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $levels = DB::table('levels')->where(function($query) use ($search_text) {
            $query->where('levels.level_name', 'LIKE', '%'.$search_text.'%')->orWhere('levels.level', 'LIKE', '%'.$search_text.'%');
        })->orderBY('id', 'asc')->paginate('10');
        $data = [
            'levels' => $levels,
        ];
        return view('admin.users.levels._item', $data)->render();
    }
}
