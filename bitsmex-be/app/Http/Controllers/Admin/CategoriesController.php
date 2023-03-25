<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\categories;
use DB;

class CategoriesController extends Controller
{
    public function getIndex() {
        $categories = categories::orderBy('id', 'desc')->paginate(10);
        foreach($categories as $key => $value) {
            $categories[$key] = $value;
            $categories[$key]->post_total = DB::table('post_category')->where('category_id', $value->id)->count();
        }
        $data = [
            'categories' => $categories,
        ];
        return view('admin.categories.index', $data);
    }

    public function getAdd() {
        return view('admin.categories.add');
    }

    public function postAdd(Request $request) {
        $this->validate($request, ['name' => 'required|string']);
        $data = [
            'name' => $request->name,
            'slug' => $this->checkSlug_exists('categories', $request->name),
            'created_at' => date(now()),
        ];
        DB::table('categories')->insert($data);

        return redirect()->back()->with('success', 'Add successful categories.');
    }

    public function getEdit($id) {
        $categories = categories::find($id);
        if(is_null($categories)) {
            abort('404');
        }

        return view('admin.categories.edit', compact('categories'));
    }

    public function postEdit(Request $request, $id) {
        $categories = categories::find($id);
        if(is_null($categories->first())) {
            abort('404');
        }
        $this->validate($request, ['name' => 'required|string|max:255']);
        $data = [
            'name' => $request->name,
            'slug' => $this->checkSlug_exists('categories', $request->name, $id),
            'created_at' => date(now()),
        ];
        DB::table('categories')->update($data);

        return redirect()->back()->with('success', 'Update successful');
    }

    public function getDelete($id) {
        $categories = categories::find($id);
        if(is_null($categories->first())) {
            abort('404');
        }
        $categories->delete();

        return redirect()->back()->with('success', 'Delete successful');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        
        $categories = DB::table('categories')->where(function($query) use ($search_text) {
            $query->where('name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('slug', 'LIKE', '%'.$search_text.'%')
            ->orWhere('created_at', 'LIKE', '%'.$search_text.'%');
        })->orderBY('id', 'desc')->paginate('10');
        foreach($categories as $key => $value) {
            $categories[$key] = $value;
            $categories[$key]->post_total = DB::table('post_category')->where('category_id', $value->id)->count();
        }
        $data = [
            'categories' => $categories,
        ];
        return view('admin.categories._item', $data)->render();
    }
}
