@extends('admin.app')
@section('title', 'Chi tiết nạp tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Chi tiết nạp tiền - <span class="text-success text-uppercase">#{{ $deposit->deposit_id }}</span></h3>
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
									<h2 class="text-info"><span class="text-uppercase">Thông tin người nạp</h2>
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
									<h2 class="text-info"><span class="text-uppercase">Thông tin nạp tiền</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Mã nạp tiền</th>
												<td>{{ $deposit->deposit_id }}</td>
											</tr>
											<tr>
												<th>ID giao dịch</th>
												<td>{{ is_null($deposit->txhash) ? 'Admin' : $deposit->txhash }}</td>
											</tr>
											<tr>
												<th>Loại tiền nạp</th>
												<td>{{ $deposit->symbol }}</td>
											</tr>
											<tr>
												<th>Số tiền nạp</th>
												<td>{{ $deposit->symbol == 'VND' ? number_format($deposit->amount) : number_format($deposit->amount, 8) }} {{ $deposit->symbol }}</td>
											</tr>
											<tr>
												<th>Phí nạp tiền</th>
												<td>{{ $deposit->symbol == 'VND' ? number_format($deposit->fee) : number_format($deposit->fee, 8) }} {{ $deposit->symbol }}</td>
											</tr>
											<tr>
												<th>Nạp tiền lúc</th>
												<td>{{ $deposit->created_at }}</td>
											</tr>
											<tr>
												<th>Trạng thái nạp tiền</th>
												<td>{!! $deposit_status[$deposit->status] !!}</td>
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