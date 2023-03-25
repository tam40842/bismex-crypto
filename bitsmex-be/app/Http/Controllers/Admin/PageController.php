<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Posts;
use App\User;
use DB;
use Auth;

class PageController extends Controller
{
    public function index() {
        $posts = DB::table('posts')->leftjoin('users', 'users.id', '=', 'posts.post_author')
        ->select('posts.*', 'users.username as username')->orderBy('posts.id', 'desc')->paginate(10);
        $data = [
            'posts' => $posts
        ];
        return view('admin.pages.index', $data);
    }

    public function getAdd() {
        $data = [
            'action' => 'add'
        ];
        return view('admin.pages.add', $data);
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'post_title' => 'required|string',
            'post_content' => 'required|string'
        ]);
        $data = [
            'post_title' => $request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title),
            'post_content' => $request->post_content,
            'post_author' => Auth::user()->id,
            'post_type' => 'page'
        ];
        $post = Posts::create($data);
        return redirect()->route('admin.pages')->with('alert_success', 'Thêm trang mới thành công.');
    }

    public function getEdit($id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->route('admin.pages')->with('alert_error', 'Trang không tồn tại.');
        }
        $data = [
            'action' => 'edit',
            'post' => $post
        ];
        return view('admin.pages.add', $data);
    }

    public function postEdit(Request $request, $id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->route('admin.pages')->with('alert_error', 'Trang không tồn tại.');
        }
        $this->validate($request, [
            'post_title' => 'required|string',
            'post_content' => 'required|string'
        ]);
        $data = [
            'post_title' => $request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title, $id),
            'post_content' => $request->post_content,
            'post_author' => Auth::user()->id,
        ];
        $post->update($data);
        return redirect()->route('admin.pages')->with('alert_success', 'Cập nhật trang thành công.');
    }

    public function getDelete($id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->back()->with('alert_error', 'Trang không tốn tại.');
        }
        $post->delete();
        return redirect()->back()->with('alert_success', 'Xóa trang thành công.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        
        $posts = DB::table('posts')->leftjoin('users', 'posts.post_author', '=', 'users.id')->where(function($query) use ($search_text) {
            $query->where('posts.post_title', 'LIKE', '%'.$search_text.'%')
            ->where('posts.post_content', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%');
        })->select('posts.*', 'users.username as username')->orderBY('id', 'desc')->paginate('10');
        $data = [
            'posts' => $posts,
        ];
        return view('admin.pages._item', $data)->render();
    }
}
