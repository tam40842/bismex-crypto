@extends('admin.app')
@section('title', 'Quản lý nạp tiền')
@section('content')
<div class="content_wrapper">
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.transfers.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Từ ngày</strong></label>
								<input type="date" value="{{ @$filter['start_day'] }}" class="form-control" name="start_day" required>
							</div>
							<div class="form-group">
								<label for="from"><strong>Đến ngày</strong></label>
								<input type="date" value="{{ @$filter['end_day'] }}" class="form-control" name="end_day" required>
							</div>
							<div class="form-group">
								<label for="status"><strong>Trạng thái</strong></label>
								<select name="status" class="form-control" id="status">
									<option {{ (@$filter['status'] == 0) ? 'selected="selected"' : '' }} value="0">Chưa xác nhận</option>
									<option {{ (@$filter['status'] == 1) ? 'selected="selected"' : '' }} value="1">Hoàn tất</option>
									<option {{ (@$filter['status'] == 2) ? 'selected="selected"' : '' }} value="2">Hủy bỏ</option>
								</select>
							</div>
							<div class="form-group">
								<label for="status"><strong>Loại thành viên</strong></label>
								<select name="admin_setup" class="form-control" id="status">
									<option {{ isset($filter) && $filter['admin_setup'] == 0 ? 'selected' : ''  }} value="0">User active</option>
									<option {{ isset($filter) && $filter['admin_setup'] == 1 ? 'selected' : ''  }} value="1">User setup</option>
								</select>
							</div>
							<div class="form-group">
								<label for="paginate"><strong>Chọn hiển thị</strong></label>
								<select name="paginate" class="form-control" id="paginate">
									<option {{ (@$filter['paginate'] == 10) ? 'selected="selected"' : '' }} value="10">10</option>
									<option {{ (@$filter['paginate'] == 50) ? 'selected="selected"' : '' }} value="50">50</option>
									<option {{ (@$filter['paginate'] == 100) ? 'selected="selected"' : '' }} value="100">100</option>
									<option {{ (@$filter['paginate'] == 200) ? 'selected="selected"' : '' }} value="200">200</option>
									<option {{ (@$filter['paginate'] == 500) ? 'selected="selected"' : '' }} value="500">500</option>
								</select>
							</div>
							<button type="submit" class="btn btn-default mt-4">Filter</button>
						</div>
					</form>
				</div>
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
					<form action="{{route('admin.transfers.search')}}" method="get">
						<input type="text" name="search_text" class="form-control table_search_text" placeholder="Keyword...">
						<span class="clear_search"><i class="glyphicon glyphicon-remove"></i></span>
						<button type="submit" class="btn btn-default table_search_submit">Search</button>
					</form>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="table-responsive-sm">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Mã chuyển tiền</th>
							<th>Tên người gửi</th>
							<th>Tên người nhận</th>
							<th>Số lượng</th>
							<th>Phí Chuyển tiền</th>
							<th>Tổng cộng</th>
							<th>Trạng thái</th>
							<th>User setup</th>
							<th>Thực hiện lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.transfers._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $transfers->count() . ' of ' . $transfers->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $transfers->appends(request()->all())->links() !!}
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