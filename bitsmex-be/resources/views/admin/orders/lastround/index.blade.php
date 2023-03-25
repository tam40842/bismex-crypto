@extends('admin.app')
@section('title', 'Last round')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Last round</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.lastround.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Date from</strong></label>
								<input type="date" class="form-control" name="date_from" required="required" value="{{ @$filter['date_from'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Date to</strong></label>
								<input type="date" class="form-control" name="date_to" required="required" value="{{ @$filter['date_to'] }}" />
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
						<form action="{{route('admin.lastround.search')}}" method="get">
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
							<th>Market</th>
							<th>Open</th>
							<th>High</th>
							<th>Low</th>
							<th>Close</th>
							<th>Adjust result</th>
							<th>Adjust Author</th>
							<th>Created date</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.orders.lastround._item')
					</tbody>
					<tfoot>
						<tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Round ID</th>
							<th>Market</th>
							<th>Open</th>
							<th>High</th>
							<th>Low</th>
							<th>Close</th>
							<th>Adjust result</th>
							<th>Adjust Author</th>
							<th>Created date</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $histories->count() . ' of ' . $histories->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $histories->appends(request()->all())->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@stop