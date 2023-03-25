@extends('admin.app')
@section('title', 'All transactions')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>All transactions</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		{{-- <div class="row">
            @foreach($markets as $key => $value)
            <div class="col-6 col-md-3 grid-margin stretch-card">
				<div class="card card-statistics">
					<div class="card-body">
						<p class="text-primary text-center"><strong>Đã giao dịch {{ $value->market_name }}</strong></p>
						<div class="clearfix">
							<div class="float-left">
								<img src="{{ $value->logo }}" alt="Vietnam Đồng" style="width: 30px;">
							</div>
							<div class="float-right">
								<div class="fluid-container">
									<p class="mb-0 text-right">{{ $value->market_name }}</p>
									<h3 class="font-weight-medium text-right mb-0">${{ number_format($sum[$value->market_name], 2) }}</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
            @endforeach
        </div> --}}
		<div class="row">
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
                                    <p class="mb-0 text-right">Win total</p>
                                    <h3 class="font-weight-medium text-right mb-0">
                                        {{ number_format($stastics['win'], 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <p class="mb-0 text-right">Lose total</p>
                                    <h3 class="font-weight-medium text-right mb-0">
                                        {{ number_format($stastics['lose'], 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.orders.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Date from</strong></label>
								<input type="date" class="form-control" name="start_day" required="required" value="{{ @$filter['start_day'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Date to</strong></label>
								<input type="date" class="form-control" name="end_day" required="required" value="{{ @$filter['end_day'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Market name</strong></label>
								<select class="form-control" name="market_name">
									<option value="">All</option>
									@foreach($markets as $key => $market)
									<option value="{{ $market->market_name }}"{{ $market->market_name==@$filter['market_name']?' selected="selected"':'' }}>{{ $market->market_name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label for="from"><strong>Type</strong></label>
								<select class="form-control" name="action">
									<option value="">All</option>
									<option value="BUY"{{ @$filter['action']=='BUY'?' selected="selected"':'' }}>BUY</option>
									<option value="SELL"{{ @$filter['action']=='SELL'?' selected="selected"':'' }}>SEL</option>
								</select>
							</div>
							<div class="form-group">
								<label for="from"><strong>Hiển thị</strong></label>
								<select class="form-control" name="perpage">
									@foreach($listperpage as $key => $row)
									<option value="{{ $key }}"{{ $key==@$filter['perpage']?' selected="selected"':'' }}>{{ $row }}</option>
									@endforeach
								</select>
							</div>
							<button type="submit" class="btn btn-default mt-4">Filter</button>
						</div>
					</form>
				</div>
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
					<form action="{{route('admin.orders.search')}}" method="get">
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
							<th>Round ID</th>
							<th>Username</th>
							<th>Type</th>
							<th>Market name</th>
							<th>Amount</th>
							<th>Profit Percent</th>
							<th>Profit</th>
							<th>Status</th>
							<th>Created date</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.orders._item')
					</tbody>
					<tfoot>
						<tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Round ID</th>
							<th>Username</th>
							<th>Type</th>
							<th>Market name</th>
							<th>Amount</th>
							<th>Profit Percent</th>
							<th>Profit</th>
							<th>Status</th>
							<th>Created date</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $orders->count() . ' of ' . $orders->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $orders->appends(request()->all())->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@stop