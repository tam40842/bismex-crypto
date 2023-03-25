@extends('admin.app')
@section('title', 'Top cao thủ')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Top cao thủ</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.users.prolist.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Date from</strong></label>
								<input type="date" class="form-control" name="date_from" required="required" value="{{ @$filter['date_from'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Date to</strong></label>
								<input type="date" class="form-control" name="date_to" required="required" value="{{ @$filter['date_to'] }}" />
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
							<th>STT</th>
							<th>Username</th>
							<th>Balance</th>
							<th>Win Total</th>
							<th>Lose Total</th>
							<th>Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.users.prolist._item')
					</tbody>
					<tfoot>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>STT</th>
							<th>Username</th>
							<th>Balance</th>
							<th>Win Total</th>
							<th>Lose Total</th>
							<th>Trạng thái</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $prolist->count() . ' of ' . $prolist->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $prolist->links() !!}
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
	table_search($('.table_search_submit'), "{{ route('admin.users.prolist.search') }}");
</script>
@endpush