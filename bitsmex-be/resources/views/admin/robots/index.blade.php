@extends('admin.app')
@section('title', 'Quản lý robots')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Quản lý robots</h3>
		<a class="button_title" href="{{ route('admin.robots.add') }}">Thêm mới</a>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
						<input type="text" class="form-control table_search_text" placeholder="Keyword">
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
							<th>Robot name</th>
							<th>Số tiền tối thiểu</th>
							<th>Số tiền tối đa</th>
							<th>Hoa hồng</th>
							<th>Phí</th>
							<th>Lợi nhuận/tháng</th>
							<TH>Số tháng</TH>
							<th>Trạng thái</th>
							<th>Cập nhật lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.robots._item')
					</tbody>
					<tfoot>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Robot name</th>
							<th>Số tiền tối thiểu</th>
							<th>Số tiền tối đa</th>
							<th>Hoa hồng</th>
							<th>Phí</th>
							<th>Lợi nhuận/tháng</th>
							<th>Số tháng</th>
							<th>Trạng thái</th>
							<th>Cập nhật lúc</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $robots->count() . ' of ' . $robots->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $robots->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="page_content pt-2">
		<div class="x_panel">
			<div class="x_title">
				<h2>Lịch sử trả lãi</h2>
			</div>
			<table class="table table-bordered">
				<thead>
					<th>Username</th>
					<th>Tên gói</th>
					<th>Mã gói</th>
					<th>Số tiền trả lãi</th>
                    <th>Phí sàn</th>
					<th>Số tiền còn lại</th>
					<th>Trạng thái</th>
					<th>Ngày trả lãi</th>
				</thead>
				<tbody>
					@if (count($histories_bonus))
						@foreach ($histories_bonus as $key => $value)
							<tr>
								<td><small>
									<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" title="Edit" target="_blank"><i class="fa fa-user fa-fw"></i>{{ $value->username }}</a>
								</small></td>
								<td><small>
									<a href="{{ route('admin.robots.edit', ['id' => $value->package_id]) }}" title="Edit" target="_blank">{{ $value->name }}</a>
								</small></td>
								<td><small>
									<a href="{{ route('admin.robots.histories', ['code' => $value->robot_code]) }}" title="Edit" target="_blank">{{ $value->robot_code }}</a>
								</small></td>
								<td>${{ number_format($value->amount, 2) }}</td>
                                <td>${{ number_format($value->amount * $value->fee / 100, 2) }}  ({{ $value->fee }}%)</td>
                                <td>${{ number_format($value->amount - ($value->amount * $value->fee / 100), 2) }}</td>
								<td>{!! $status_bonus_robot[$value->status] !!}</td>
								<td>{{ date('d-m-Y', strtotime($value->created_at)) }}</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="5" class="text-center">No results.</td>
						</tr>
					@endif
				</tbody>
			</table>
			<div class="table_bottom_actions pb-3 pr-3">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $histories_bonus->count() . ' of ' . $histories_bonus->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $histories_bonus->appends(request()->all())->links() !!}
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
	table_search($('.table_search_submit'), "{{ route('admin.robots.search') }}");
</script>
@endpush