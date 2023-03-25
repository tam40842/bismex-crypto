@extends('admin.app')
@section('title', 'Quản lý exchange')
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
							<th>Username</th>
							<th>Symbol</th>
							<th>Số lượng</th>
							<th>Tỷ giá</th>
							<th>Tổng cộng</th>
							<th>Thực hiện lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.exchange._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $exchange->count() . ' of ' . $exchange->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $exchange->appends(request()->all())->links() !!}
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