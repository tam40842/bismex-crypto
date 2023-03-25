<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Posts;
use DB;
use Auth;

class PostController extends Controller
{
    public function index() {
        $data = [
            'posts' => Posts::where('post_type', 'post')->orderBy('id', 'desc')->paginate(10)
        ];
        return view('admin.posts.index', $data);
    }

    public function getAdd() {
        $data = [
            'title' => __('Edit post')
        ];
        return view('admin.posts.add', $data);
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'post_title' => 'required|string|max:255',
            'post_content' => 'required',
            'post_img' => 'required|url',
            'category' => 'required',
        ]);
        if(!array_key_exists($request->category, config('posts.post.categories'))) {
            return redirect()->back()->withInput()->with('alert_error', 'Category does not exists.');
        }

        Posts::create([
            'post_title' => (string)$request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title),
            'post_content' => (string)$request->post_content,
            'post_img' => $request->post_img,
            'post_author' => Auth::user()->id,
            'post_categories' => $request->category,
            'post_tags' => (string)$request->post_tags
        ]);

        return redirect()->route('admin.posts')->with('alert_success', 'Post added successfully.');
    }

    public function getEdit($id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->back()->with('alert_error', 'Post does not exists.');
        }
        $data = [
            'post' => $post,
            'post_tags' => explode(',', $post->post_tags),
            'title' => __('Edit post')
        ];
        
        return view('admin.posts.add', $data);
    }

    public function postEdit(Request $request, $id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->back()->with('alert_error', 'Post does not exists.');
        }
        $this->validate($request, [
            'post_title' => 'required|string|max:255',
            'post_content' => 'required',
            'post_img' => 'required|url',
            'category' => 'required',
        ]);
        if(!array_key_exists($request->category, config('posts.post.categories'))) {
            return redirect()->back()->withInput()->with('alert_error', 'Category does not exists.');
        }
        $post->update([
            'post_title' => (string)$request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title, $id),
            'post_content' => (string)$request->post_content,
            'post_img' => $request->post_img,
            'post_categories' => $request->category,
            'post_tags' => (string)$request->post_tags
        ]);
        return redirect()->route('admin.posts')->with('alert_success', 'Post updated successfully.');
    }

    public function getDelete($id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            return redirect()->back()->with('alert_error', 'Post does not exists.');
        }
        $post->delete();
        return redirect()->route('admin.posts')->with('alert_success', 'Post deleted successfully.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $posts = Posts::where(function($query) use ($search_text) {
            $query->where('post_title', 'LIKE', '%'.$search_text.'%')
            ->orWhere('post_content', 'LIKE', '%'.$search_text.'%');
        })->orderBY('id', 'desc')->paginate('10');
        $data = [
            'posts' => $posts,
        ];
        return view('admin.posts._item', $data)->render();
    }
}
