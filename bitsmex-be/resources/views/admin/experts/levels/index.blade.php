@extends('admin.app')
@section('title', 'Quản lý Level commission')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Quản lý Level commission</h3>
		<a class="button_title" href="{{ route('admin.levels.add') }}">Thêm mới</a>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
						<input type="text" class="form-control table_search_text" placeholder="Keyword">
						<span class="clear_search"><i class="glyphicon glyphicon-remove"></i></span>
						<button type="button" class="btn btn-default table_search_submit">Search</button>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="table-responsive-sm">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Level name</th>
							<th>Số tầng</th>
							<th>Hoa hồng</th>
							<th>Tạo lúc</th>
							<th>Cập nhật lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.users.levels._item')
					</tbody>
					<tfoot>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Level name</th>
							<th>Số tầng</th>
							<th>Hoa hồng</th>
							<th>Tạo lúc</th>
							<th>Cập nhật lúc</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $levels->count() . ' of ' . $levels->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $levels->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@stop
@push('css')
<style>
	.table_user_avatar .img_wrapper{
		float: left;
		width: 32px;
		height: 32px;
	}
	.table_user_roles{
		margin: 0;
		padding: 0;
		list-style: none;
	}
</style>
@endpush
@push('js')
<script type="text/javascript">
	table_search($('.table_search_submit'), "{{ route('admin.levels.search') }}");
</script>
@endpush