@extends('admin.app')
@section('title', 'AutoTrade Manage')
@section('content')
<div class="content_wrapper">
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.autotrade.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Từ ngày</strong></label>
								<input type="date" value="{{ @$filter['start_day'] }}" class="form-control" name="start_day">
							</div>
							<div class="form-group">
								<label for="from"><strong>Đến ngày</strong></label>
								<input type="date" value="{{ @$filter['end_day'] }}" class="form-control" name="end_day">
                            </div>
							<div class="form-group">
								<label for="status"><strong>Trạng thái</strong></label>
								<select name="status" class="form-control" id="status">
                                    <option {{ (@$filter['status'] == null) ? 'selected="selected"' : '' }} value="">TẤT CẢ</option>
									<option {{ (@$filter['status'] == '0') ? 'selected="selected"' : '' }} value="0">Hủy</option>
                                    <option {{ (@$filter['status'] == '1') ? 'selected="selected"' : '' }} value="1">Đang chạy</option>
                                    <option {{ (@$filter['status'] == '2') ? 'selected="selected"' : '' }} value="2">Hoàn tất</option>
								</select>
                            </div>
                            <div class="form-group">
								<label for="keyword"><strong>Keyword</strong></label>
                                <input type="text" value="{{ @$filter['keyword'] }}" class="form-control"  class="form-control table_search_text" name="keyword" autocomplete="off">
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
				<div class="clearfix"></div>
			</div>
			<div class="table-responsive-sm">
				<table class="table table-bordered">
					<thead>
						<tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>Username</th>
							<th>Package ID</th>
							<th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Đã nhận</th>
                            <th>Mượn</th>
							<th>Yêu cầu rút</th>
							<th>Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.autotrade._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $packages->count() . ' of ' . $packages->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $packages->links() !!}
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