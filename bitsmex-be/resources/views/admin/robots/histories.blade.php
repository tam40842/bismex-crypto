@extends('admin.app')
@section('title', 'Lịch sử Robot')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Gói {{ $robot_order->name.' code #'.$robot_order->robot_code }}</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content pt-3	">
		<div class="x_panel">
			<div class="x_title">
				<h2>Thông tin gói</h2>
			</div>
			<table class="table table-bordered">
				<thead>
					<th>Ngày mua</th>
					<th>Ngày hết hạn</th>
					<th>Số tháng</th>
					<th>Username</th>
					<th>Số tiền</th>
					<th>Phí mua gói/tháng</th>
					<th>Thành viên tuyến trên</th>
					<th>Tiền tuyến trên hưởng</th>
					<th>Trạng thái</th>
				</thead>
				<tbody>
                    <tr>
                        <td>{{ date('d-m-Y', strtotime($robot_order->created_at)) }}</td>
                        <td>{{ \carbon\Carbon::parse($robot_order->created_at)->addMonths($robot_order->month)->format('d-m-Y') }}</td>
                        <td><span class="badge badge-success">{{ $robot_order->month }} tháng</span></td>
                        <td>
                            <small>
                                <a href="{{ route('admin.users.edit', ['id' => $robot_order->userid]) }}" title="Edit" target="_blank"><i class="fa fa-user fa-fw"></i>{{ $robot_order->username }}</a>
                            </small>
                        </td>
                        <td>${{ number_format($robot_order->amount, 2) }}</td>
                        <td>{{ $robot_order->fee }}%</td>
                        <td>
                            @if(is_null($user_bonus))
                                ---
                            @else
                            <small>
                                <a href="{{ route('admin.users.edit', ['id' => $user_bonus->userid]) }}" title="Edit" target="_blank"><i class="fa fa-user fa-fw"></i>{{ $user_bonus->username }}</a>
                            </small>
                            @endif
                        </td>
                        <td>${{ !is_null($user_bonus) ? number_format($user_bonus->amount, 2) : number_format(0, 2) }}</td>
                        <td>{!! $status_robot[$robot_order->status] !!}</td>
                    </tr>
				</tbody>
			</table>
			<div class="table_bottom_actions pb-3 pr-3">
				<div class="table_bottom_actions_left">
					
				</div>
				{{-- <div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $histories->count() . ' of ' . $histories->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $histories->appends(request()->all())->links() !!}
				</div> --}}
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
					<th>Ngày trả lãi</th>
					<th>Số tiền trả lãi</th>
                    <th>Phí sàn</th>
					<th>Số tiền còn lại</th>
					<th>Trạng thái</th>
				</thead>
				<tbody>
					@if (count($histories_bonus))
						@foreach ($histories_bonus as $key => $value)
							<tr>
								<td>{{ date('d-m-Y', strtotime($value->created_at)) }}</td>
								<td>${{ number_format($value->amount, 2) }}</td>
                                <td>${{ number_format($value->amount * $robot_order->fee / 100, 2) }}</td>
                                <td>${{ number_format($value->amount - ($value->amount * $robot_order->fee / 100), 2) }}</td>
								<td>{!! $status_bonus_robot[$value->status] !!}</td>
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
@include('admin.includes.boxes.media')
@stop