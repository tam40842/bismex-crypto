@extends('admin.app')
@section('title', 'Chi tiết chuyển tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Chi tiết chuyển tiền - <span class="text-success text-uppercase">#{{ $transfers->transfer_id }}</span></h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info"><span class="text-uppercase">Thông tin người chuyển</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Username</th>
												<td>{{ $user->username }}</td>
											</tr>
											<tr>
												<th>Họ tên</th>
												<td>{{ $user->first_name.' '.$user->last_name }}</td>
											</tr>
											<tr>
												<th>Số trên giấy tờ</th>
												<td><strong class="text-danger">{{ $user->identity_number }}</strong></td>
											</tr>
											<tr>
												<th>Số điện thoại</th>
												<td>{{ $user->phone_number }}</td>
											</tr>
											<tr>
												<th>Địa chỉ Email</th>
												<td>{{ $user->email }}</td>
											</tr>
											<tr>
												<th>Trạng thái xác minh</th>
												<td>{!! $user->kyc_status ? '<span class="badge badge-success">Đã xác minh</span>' : '<span class="badge badge-danger">Chưa xác minh</span>' !!}</td>
											</tr>
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info"><span class="text-uppercase">Thông tin chuyển tiền</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Mã chuyển tiền</th>
												<td>{{ $transfers->transfer_id }}</td>
											</tr>
											<tr>
												<th>Người chuyển</th>
												<td>{{ $transfers->sender  }}</td>
											</tr>
											<tr>
												<th>Người nhận</th>
												<td>{{ $transfers->receiver  }}</td>
											</tr>
											<tr>
												<th>Số lượng</th>
												<td>{{ number_format($transfers->amount) }}</td>
											</tr>
											<tr>
												<th>Phí chuyển</th>
												<td>{{ $transfers->fee }}</td>
											</tr>
											<tr>
												<th>Tổng cộng</th>
												<td>{{ number_format($transfers->total) }}</td>
											</tr>
											<tr>
												<th>chuyển tiền lúc</th>
												<td>{{ $transfers->created_at }}</td>
											</tr>
											<tr>
												<th>Trạng thái chuyển tiền</th>
												<td>{!! $transfers_status[$transfers->status] !!}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@stop
@push('css')

@endpush
@push('js')

@endpush