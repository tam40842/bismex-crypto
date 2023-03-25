@extends('admin.app')
@section('title', 'Categories')
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>Categories</h3>
        <a class="button_title" href="{{ route('admin.categories.add') }}">Add Category</a>
    </div>
    <div class="page_content">
        <div class="datatable">
            <div class="table_top_actions">
                <div class="table_top_actions_right">
                    <img class="search_loading" src="" alt="Search Loading">
                    <div class="table_search">
                        <input type="text" class="form-control table_search_text" placeholder="Keyword...">
                        <span class="clear_search"><i class="glyphicon glyphicon-remove"></i></span>
                        <button type="button" class="btn btn-default table_search_submit">Search</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
            @endif
            <div class="table-responsive-sm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>Category name</th>
                            <th>Post count</th>
                            <th>Created date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.categories._item')
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>Category name</th>
                            <th>Post count</th>
                            <th>Created date</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="table_bottom_actions">
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $categories->count() . ' of ' . $categories->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $categories->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
	table_search($('.table_search_submit'), "{{ route('admin.categories.search') }}");
</script>
@endpush