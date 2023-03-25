@extends('admin.app')
@section('title', 'Tracking Balance')
@section('content')
<div class="content_wrapper">
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.transactions.filters') }}" method="get">
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
								<label for="wallet_type"><strong>Trạng thái</strong></label>
								<select name="wallet_type" class="form-control" id="wallet_type">
                                    <option {{ (@$filter['wallet_type'] == null) ? 'selected="selected"' : '' }} value="">TẤT CẢ</option>
                                    <option {{ (@$filter['wallet_type'] == 'live_balance') ? 'selected="selected"' : '' }} value="live_balance">LIVE_BALANCE</option>
                                    <option {{ (@$filter['wallet_type'] == 'primary_balance') ? 'selected="selected"' : '' }} value="primary_balance">PRIMARY_BALANCE</option>
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
							<th>Loại ví</th>
							<th>Tên sự kiện</th>
                            <th>Số dư ban đầu</th>
                            <th>Số tiền thay đổi</th>
                            <th>Số dư cuối</th>
							<th>Nội dung</th>
                            <th>Ngày thay đổi</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.transactions._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $transactions->count() . ' of ' . $transactions->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $transactions->links() !!}
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