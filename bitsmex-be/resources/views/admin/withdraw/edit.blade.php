@extends('admin.app')
@section('title', 'Chi tiết rút tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Chi tiết rút tiền - <span class="text-success text-uppercase">#{{ $withdraw->withdraw_id }}</span></h3>
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
									<h2 class="text-info"><span class="text-uppercase">Thông tin người rút</h2>
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
									<p>
										<img src="https://chart.googleapis.com/chart?chs=200x200&chld=L|1&cht=qr&chl={{ $withdraw->output_address }}" alt="">
									</p>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info"><span class="text-uppercase">Thông tin rút tiền</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Mã rút tiền</th>
												<td>{{ $withdraw->withdraw_id }}</td>
											</tr>
											<tr>
												<th>Loại tiền rút</th>
												<td>{{ $withdraw->symbol }}</td>
											</tr>
											<tr>
												<th>Số tiền rút</th>
												<td>{{ $withdraw->symbol == 'VND' ? number_format($withdraw->amount) : number_format($withdraw->amount, 8) }} {{ $withdraw->symbol }}</td>
											</tr>
											<tr>
												<th>Phí rút tiền</th>
												<td>{{ $withdraw->symbol == 'VND' ? number_format($withdraw->fee) : number_format($withdraw->fee, 8) }} {{ $withdraw->symbol }}</td>
											</tr>
											<tr>
												<th>Tổng cộng</th>
												<td>{{ $withdraw->symbol == 'VND' ? number_format($withdraw->fee) : number_format($withdraw->amount - $withdraw->fee, 8) }} {{ $withdraw->symbol }}</td>
											</tr>
											<tr>
												<th>Địa chỉ ví rút</th>
												<td>
													<input type="text" class="form-control" disabled value="{{ $withdraw->output_address }}">
												</td>
											</tr>
											<tr>
												<th>Tx Hash</th>
												<td>
													<textarea class="form-control" rows="3" disabled>{{ is_null($withdraw->txhash) ? 'unknown' : $withdraw->txhash }}</textarea>
												</td>
											</tr>
											<tr>
												<th>Tạo lệnh lúc</th>
												<td>{{ $withdraw->created_at }}</td>
											</tr>
											<tr>
												<th>Trạng thái rút tiền</th>
												<td>{!! $withdraw_status[$withdraw->status] !!}</td>
											</tr>
											@if($withdraw->status == 0)
											<tr>
												<th></th>
												<td>
													<a href="{{ route('admin.withdraw.approved', ['withdraw_id' => $withdraw->withdraw_id]) }}" class="btn btn-success text-white" id="withdraw_process"><i class="fa fa-check fa-fw"></i>Duyệt lệnh</a>

													<a href="{{ route('admin.withdraw.cancelled', ['withdraw_id' => $withdraw->withdraw_id]) }}" class="btn btn-danger text-white" id="withdraw_process"><i class="fa fa-times fa-fw"></i>Từ chối</a>
												</td>
											</tr>
											@endif
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
<div class="modal" id="2fa">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="get">
				<div class="modal-header">
					<h4 class="modal-title">Mã xác nhận 2FA của bạn</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<!-- <div class="form-input">
						<label for="txhash"><strong>TxHash</strong></label>
						<input type="text" class="form-control" placeholder="Transaction tx hash" name="txhash" required>
					</div> -->
					<div class="form-input mt-3">
						<label for="txhash"><strong>Two-Factor Code</strong></label>
						<input type="text" class="form-control" placeholder="Nhập mã Authy" name="twofa_code" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Tiếp tục</button>
				</div>
			</form>
		</div>
	</div>
</div>
@stop
@push('css')

@endpush
@push('js')
<script>
	$(document).on('click', '#withdraw_process', function() {
		var href = $(this).attr('href');
		$('#2fa').modal('show').find('form').attr('action', href);
		return false;
	});
</script>
@endpush