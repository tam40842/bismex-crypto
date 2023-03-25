@extends('admin.app')
@section('title', 'Quản lý rút tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Quản lý rút tiền</h3>
		<a class="button_title" href="{{ route('admin.withdraw.add') }}">Trừ tiền User</a>
	</div>
	<div class="row">
		@foreach($stastics_usd as $key => $value)
		<div class="col-md-3 col-6 grid-margin stretch-card">
			<div class="card card-statistics">
				<div class="card-body">
					<div class="clearfix">
						<div class="float-left">
							<div class="fluid-container">
								<i class="menu-icon fa fa-2x text-warning">USD</i>
							</div>
						</div>
						<div class="float-right">
							<div class="fluid-container">
								<p class="mb-0 text-right text-uppercase">{{ $key }}</p>
								<h3 class="font-weight-medium text-right mb-0">
									${{ number_format($value, 2) }}
								</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
	{{-- <div class="row">
		@foreach($stastics_vndc as $key => $value)
		<div class="col-md-3 col-6 grid-margin stretch-card">
			<div class="card card-statistics">
				<div class="card-body">
					<div class="clearfix">
						<div class="float-left">
							<div class="fluid-container">
								<i class="menu-icon fa fa-2x text-warning">VNDC</i>
							</div>
						</div>
						<div class="float-right">
							<div class="fluid-container">
								<p class="mb-0 text-right text-uppercase">{{ $key }}</p>
								<h3 class="font-weight-medium text-right mb-0">
									VND{{ number_format($value) }}
								</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div> --}}
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.withdraw.filters') }}" method="get">
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
									<option {{ (@$filter['status'] == 0) ? 'selected="selected"' : '' }} value="0">Chờ xác nhận</option>
									<option {{ (@$filter['status'] == 1) ? 'selected="selected"' : '' }} value="1">Hoàn tất</option>
									<option {{ (@$filter['status'] == 2) ? 'selected="selected"' : '' }} value="2">Hủy bỏ</option>
								</select>
							</div>
							<div class="form-group">
								<label for="symbol"><strong>Loại nạp</strong></label>
								<select name="symbol" class="form-control" id="symbol">
									<option value="Full">Tất cả</option>
									@foreach($currencies as $value)
									<option {{ (@$filter['symbol'] == $value->symbol) ? 'selected="selected"' : '' }} value="{{ $value->symbol }}">{{ $value->symbol }}</option>
									@endforeach
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
							<th>Mã rút tiền</th>
							<th>Username</th>
							<th>Symbol</th>
							<th>Số lượng</th>
							<th>Phí rút tiền</th>
							<th>Tổng tiền</th>
							<th>Trạng thái</th>
							<th>Tạo lệnh lúc</th>
							<th>Cập nhật lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.withdraw._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $withdraw->count() . ' of ' . $withdraw->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $withdraw->links() !!}
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
	table_search($('.table_search_submit'), "{{ route('admin.withdraw.search') }}");
</script>
@endpush
