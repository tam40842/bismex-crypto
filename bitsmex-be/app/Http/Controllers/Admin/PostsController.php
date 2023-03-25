<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\categories;
use App\Posts;
use App\User;
use Storage;
use Auth;
use DB;

class PostsController extends Controller
{
    public $categories;

    public function __construct() {
        $this->categories = categories::all();
        view()->share('categories', $this->categories);
    }

    public function getIndex() {
        $posts = Posts::where('post_type','post')->join('users', 'posts.post_author', '=', 'users.id')->orderBy('posts.id', 'desc')->select('posts.*', 'users.first_name', 'users.last_name')->paginate(10);
        $post_status = $this->post_status();
        $categories = categories::all();
        return view('admin.posts.index', compact('posts', 'categories', 'post_status'));
    }

    public function getAdd() {
        $post_status = $this->post_status();
        $categories = categories::all();
        return view('admin.posts.add', compact('categories', 'post_status'));
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'post_img' => 'required|url',
            'categories' => 'required',
            'post_status' => 'required|string',
        ]);
        $post_status = $this->post_status();
        foreach($post_status as $key => $value) {
            if(!array_key_exists($request->post_status, $post_status)) {
                return redirect()->back()->withInput()->with('alert_error', 'The post status does not exist.');
            }
        }
        foreach($request->categories as $value) {
            $category_exists = categories::find($value);
            if(is_null($category_exists)) {
                return redirect()->back()->withInput()->with('alert_error', 'The category does not exist.');
            }
        }
        $data = [
            'post_title' => $request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title),
            'post_content' => $request->post_content,
            'post_img' => $request->post_img,
            'post_author' => Auth::id(),
            'post_type' => 'post',
            'post_categories' => json_encode($request->categories),
            'created_at' => date(now()),
            'updated_at' => date(now()),
        ];
        $data['post_meta'] = $request->has('meta') ? json_encode($request->meta) : '';
        $data['post_tags'] = $request->has('post_tags') ? $request->post_tags : '';
        $post_insert = Posts::create($data);
        foreach($request->categories as $value) {
            DB::table('post_category')->insert([
                'post_id' => $post_insert->id,
                'category_id' => $value
            ]);
        }
        return redirect()->route('admin.posts')->with('alert_success', 'The post has been added successfully.');
    }

    public function getEdit($id) {
        $post = Posts::find($id);
        $categories = categories::all();
        if(is_null($post)){
            return redirect()->route('admin.posts')->with('alert_error', 'The post does not exist.');
        }
        return view('admin.posts.add', compact('post', 'categories'));
    }

    public function postEdit(Request $request, $id) {
        $post = Posts::find($id);
        if(is_null($post)){
            return redirect()->route('admin.posts')->with('alert_error', 'The post does not exist.');
        }
        $this->validate($request, [
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'post_img' => 'required|url',
            'categories' => 'required',
            'post_status' => 'required|string',
        ]);
        $post_status = $this->post_status();
        foreach($post_status as $key => $value) {
            if(!array_key_exists($request->post_status, $post_status)) {
                return redirect()->back()->withInput()->with('alert_error', 'The post status does not exist.');
            }
        }
        foreach($request->categories as $value) {
            $category_exists = categories::find($value);
            if(is_null($category_exists)) {
                return redirect()->back()->withInput()->with('alert_error', 'The category does not exist.');
            }
        }
        $data = [
            'post_title' => $request->post_title,
            'slug' => $this->checkSlug_exists('posts', $request->post_title, $id),
            'post_content' => $request->post_content,
            'post_img' => $request->post_img,
            'post_author' => Auth::id(),
            'post_categories' => json_encode($request->categories),
            'updated_at' => date(now()),
        ];
        $data['post_meta'] = $request->has('meta') ? json_encode($request->meta) : $post->post_meta;
        $data['post_tags'] = $request->has('post_tags') ? $request->post_tags : $post->post_tags;
        $post->update($data);
        DB::table('post_category')->where('post_id', $id)->delete();
        foreach($request->categories as $value) {
            DB::table('post_category')->insert([
                'post_id' => $id,
                'category_id' => $value
            ]);
        }
        return redirect()->route('admin.posts')->with('alert_success', 'Update the article successfully.');
    }

    public function getDelete($id) {
        $post = Posts::find($id);
        if(is_null($post)) {
            abort('404');
        }
        $post->delete();
        DB::table('post_category')->where('post_id', $id)->delete();
        return redirect()->back()->with('alert_success', 'Delete the article successfully.');
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
            ->orWhere('posts.post_content', 'LIKE', '%'.$search_text.'%')
            ->orWhere('posts.post_tags', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.first_name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.last_name', 'LIKE', '%'.$search_text.'%');
        })->where('posts.post_type', 'post')
        ->select('posts.*', 'users.first_name', 'users.last_name')->orderBY('id', 'desc')->paginate('10');
        $data = [
            'posts' => $posts,
            'post_status' => $this->post_status()
        ];
        return view('admin.posts._item', $data)->render();
    }
}
