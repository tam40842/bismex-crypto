<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use App\Advantages;

class AdvantagesController extends Controller
{
    public function index() {
        $data = [
            'advantages' => Advantages::orderBy('id', 'desc')->paginate(10)
        ];
        return view('admin.advantages.index', $data);
    }

    public function getAdd() {

        return view('admin.advantages.add');
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'image' => 'required|url',
            'content' => 'required|string'
        ]);
        Advantages::create([
            'name' => $request->name,
            'image' => $request->image, 
            'content' => $request->content,
        ]);
        Session::flash('notify_type', 'success');
        Session::flash('notify_content', 'The advantages added successfully.');
        return redirect()->route('admin.advantages');
    }

    public function getEdit($id) {
        $advan = Advantages::find($id);
        if(is_null($advan)) {
            Session::flash('notify_type', 'error');
            Session::flash('notify_content', 'The advantages does not exits.');
            return redirect()->back();
        }
        $data = [
            'advantage' => $advan
        ];
        return view('admin.advantages.add', $data);
    }

    public function postEdit(Request $request, $id) {
        $advan = Advantages::find($id);
        if(is_null($advan)) {
            Session::flash('notify_type', 'error');
            Session::flash('notify_content', 'The advantages does not exits.');
            return redirect()->back();
        }
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'image' => 'required|url',
            'content' => 'required|string'
        ]);
        $advan->update([
            'name' => $request->name,
            'image' => $request->image, 
            'content' => $request->content,
        ]);
        Session::flash('notify_type', 'success');
        Session::flash('notify_content', 'The advantages updated successfully.');
        return redirect()->route('admin.advantages');
    }
}
