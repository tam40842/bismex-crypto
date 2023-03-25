@extends('admin.app')
@section('title', 'Lịch sử commission')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Lịch sử commission</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="row">
			@foreach($commission_type as $key => $value)
			<div class="col-md-6 col-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <div class="fluid-container">
                                    <i class="menu-icon fa fa-usd fa-fw fa-3x text-warning"></i>
                                </div>
                            </div>
                            <div class="float-right">
                                <div class="fluid-container">
                                    <p class="mb-0 text-right">{{ $value }}</p>
                                    <h3 class="font-weight-medium text-right mb-0">
                                        ${{ number_format($commission_sum[$key], 2) }}
									</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			@endforeach
		</div>
		<div class="datatable pt-4">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.commissions.filters') }}" method="get">
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
								<label for="from"><strong>Loại</strong></label>
								<select class="form-control" name="type">
									<option value="">All</option>
									<option value="trade" {{ @request()->type == 'trade' ? 'selected' : '' }}>Trade</option>
									<option value="bonus" {{ @request()->type == 'bonus' ? 'selected' : '' }}>Bonus</option>
								</select>
							</div>
							<button type="submit" class="btn btn-default mt-4">Filter</button>
						</div>
					</form>
				</div>
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
					<form action="{{route('admin.commissions.search')}}" method="get">
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
							<th>Số tiền</th>
							<th>Loại hoa hồng</th>
							<th>Nội dung</th>
							{{-- <th>Tuần</th> --}}
							<th>Cập nhật lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.commissions._item')
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $commissions->count() . ' of ' . $commissions->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $commissions->appends(request()->all())->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@endsection
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