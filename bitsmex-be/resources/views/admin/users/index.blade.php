@extends('admin.app')
@section('title', 'Quản lý tài khoản')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Quản lý tài khoản</h3>
		<a class="button_title mr-2" href="{{ route('admin.users.add') }}">Thêm mới</a>
		{{-- <a href="{{ route('admin.users.export') }}"  class="button_title">Export</a> --}}
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
			<div class="table_top_actions_left">
					<form action="{{ route('admin.users.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="status"><strong>Trạng thái</strong></label>
								<select name="status" class="form-control" id="status">
									<option {{ isset($filter) && $filter['status'] == 1 ? 'selected' : ''  }} value="1">Đã kích hoạt</option>
									<option {{ isset($filter) && $filter['status'] == 0 ? 'selected' : ''  }} value="0">Chưa kích hoạt</option>
									<option {{ isset($filter) && $filter['status'] == 2 ? 'selected' : ''  }} value="2">Hủy bỏ</option>
								</select>
							</div>
							<div class="form-group px-1">
								<label for="status"><strong>Loại thành viên</strong></label>
								<select name="admin_setup" class="form-control" id="status">
									<option {{ isset($filter) && $filter['admin_setup'] == 0 ? 'selected' : ''  }} value="0">User active</option>
									<option {{ isset($filter) && $filter['admin_setup'] == 1 ? 'selected' : ''  }} value="1">User setup</option>
								</select>
							</div>
							<button type="submit" class="btn btn-default mt-4">Filter</button>
						</div>
					</form>
				</div>
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
						<input type="text" class="form-control table_search_text" placeholder="Keyword...">
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
							<th>Username</th>
							<th>Email</th>
							<th>KYC</th>
							<th>Trạng thái</th>
							<th>Total volume</th>
							<th>Live Balance</th>
							<th>User setup</th>
							<th>Quyền</th>
							<th>Tham gia lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.users._item')
					</tbody>
					<tfoot>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Username</th>
							<th>Email</th>
							<th>KYC</th>
							<th>Trạng thái</th>
							<th>Total volume</th>
							<th>Live Balance</th>
							<th>User setup</th>
							<th>Quyền</th>
							<th>Tham gia lúc</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $users->count() . ' of ' . $users->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $users->links() !!}
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
	table_search($('.table_search_submit'), "{{ route('admin.users.search') }}");
</script>
@endpush