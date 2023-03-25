@extends('admin.app')
@section('title', 'Add New')
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>Add categories</h3>
    </div>
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <form action="" method="POST">
            @csrf
            <div class="x_panel">
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            @foreach(config('articles.post.widgets.left') as $value)
                                @include('admin.posts.widgets.'.$value)
                            @endforeach
                        </div>
                        <div class="col-md-4 col-12">
                            @foreach(config('articles.post.widgets.right') as $value)
                                @include('admin.posts.widgets.'.$value)
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('admin.includes.boxes.media')
@endsection