@extends('admin.app')
@section('title', 'Cập nhật Robot')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cập nhật Robot</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="post" enctype="multipart/form-data">
			@csrf
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<table class="admin_table">
								<tr>
									<th>Robot name</th>
									<td>
										<input type="text" name="name" class="form-control" value="{{ isset($robot->name) ? $robot->name : old('name') }}" required>
									</td>
								</tr>
								<tr>
                                    <th><label for="from">Số tiền tối thiểu</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="min" class="form-control" value="{{ isset($robot->min) ?$robot->min : old('min') }}" required>
										</div>
									</td>
                                </tr>
								<tr>
									<th><label for="from">Số tiền tối đa</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="max" class="form-control" value="{{ isset($robot->max) ?$robot->max : old('max') }}" required>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="from">Hoa hồng</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">%</span>
											<input type="text" name="bonus" class="form-control" value="{{ isset($robot->bonus) ?$robot->bonus : old('bonus') }}" required>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="from">Phí/tháng</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">%</span>
											<input type="text" name="fee" class="form-control" value="{{ isset($robot->fee) ?$robot->fee : old('fee') }}" required>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="">Lợi nhuận tháng</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">%</span>
											<input type="text" name="interest" class="form-control" value="{{ isset($robot->interest) ? $robot->interest : old('interest') }}" required>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="">Số tháng</label></th>
									<td>
										<div class="input-group">
											<input type="text" name="month" class="form-control" value="{{ isset($robot->month) ? $robot->month : old('month') }}" required>
										</div>
									</td>
								</tr>
								{{-- <tr>
									<th>Hình ảnh</th>
									<td>
										<div class="choose_img_lib post_single_image">
											<div class="input-group">
												<input type="text" name="image" class="form-control fill_img_lib" placeholder="" value="{{ isset($robot->image) ? $robot->image : old('image') }}">
												<span class="input-group-btn">
													<button type="button" class="btn btn-secondary open_img_lib" gallery="false">Choose image</button>
												</span>
											</div>
										</div>
									</td>
								</tr> --}}
								<tr>
									<th>Trạng thái</th>
									<td>
										<div class="switch_toggle">
											<input type="radio" name="actived" class="switch_on" content="Mở" value="1" {{ $robot->actived ? 'checked' : '' }}>
											<input type="radio" name="actived" class="switch_off" content="Tắt" value="0" {{ !$robot->actived ? 'checked' : '' }}>
                                        </div>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-12 text-right">
							<button class="btn btn-primary" type="submit">Lưu cài đặt</button>
						</div>
					</div>
				</div>
			</div>	
		</form>
	</div>
	<div class="page_content pt-3	">
		<div class="x_panel">
			<div class="x_title">
				<h2>Quản lý gói</h2>
			</div>
			<table class="table table-bordered">
				<thead>
					<th>Ngày mua</th>
					<th>Ngày hết hạn</th>
					<th>Số tháng</th>
					<th>Username</th>
					<th>Mã gói</th>
					<th>Số tiền</th>
					<th>Phí mua gói</th>
					<th>Thành viên tuyến trên</th>
					<th>Tiền tuyến trên hưởng</th>
					<th>Trạng thái</th>
				</thead>
				<tbody>
					@if (count($histories))
						@foreach ($histories as $key => $value)
							<tr>
								<td>{{ date('d-m-Y', strtotime($value->created_at)) }}</td>
								<td>{{ \carbon\Carbon::parse($value->created_at)->addMonths($value->month)->format('d-m-Y') }}</td>
								<td><span class="badge badge-success">{{ $value->month }} tháng</span></td>
								<td>
									<small>
										<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" title="Edit" target="_blank"><i class="fa fa-user fa-fw"></i>{{ $value->username }}</a>
									</small>
								</td>
								<td>
									<div class="table_title">
										<a href="{{ route('admin.robots.histories', ['code' => $value->robot_code]) }}" target="_blank">#{{ $value->robot_code }}</a>
									</div>
								</td>
								<td>${{ number_format($value->amount, 2) }}</td>
								<td>${{ number_format($value->amount * $value->fee / 100, 2) }}</td>
								<td>{{ isset($value->user_bonus) ? $value->user_bonus : '---' }}</td>
								<td>${{ isset($value->amount_bonus) ? number_format($value->amount_bonus, 2) : number_format(0, 2) }}</td>
								<td>{!! $status_robot[$value->status] !!}</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="10" class="text-center">No results.</td>
						</tr>
					@endif
				</tbody>
			</table>
			<div class="table_bottom_actions pb-3 pr-3">
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
@include('admin.includes.boxes.media')
@stop