@extends('admin.app')
@section('title', 'Top đại gia')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Top đại gia</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">

				</div>
				<div class="table_top_actions_right">

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
							<th>Email</th>
							<th>Balance</th>
							<th>Trạng thái</th>
							<th>Tham gia lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.users.richlist._item')
					</tbody>
					<tfoot>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>STT</th>
							<th>Username</th>
							<th>Email</th>
							<th>Balance</th>
							<th>Trạng thái</th>
							<th>Tham gia lúc</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $rich->count() . ' of ' . $rich->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $rich->links() !!}
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