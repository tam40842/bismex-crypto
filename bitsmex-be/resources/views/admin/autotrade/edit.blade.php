@extends('admin.app')
@section('title', 'Chi tiết gói autotrade')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Chi tiết gói autotrade - <span class="text-success text-uppercase">#{{ $package->package_id }}</span></h3>
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
									<h2 class="text-info"><span class="text-uppercase">Thông tin người mua</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Username</th>
												<td>{{ $package->username }}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info"><span class="text-uppercase">Thông tin gói autotrade</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Mã nạp tiền</th>
												<td>{{ $package->package_id }}</td>
											</tr>
											<tr>
												<th>Ngày bắt đầu</th>
												<td>{{ $package->start_date }}</td>
											</tr>
											<tr>
												<th>Ngày kết thúc</th>
												<td>{{ $package->end_date }}</td>
											</tr>
											<tr>
												<th>Đã mượn</th>
												<td>${{ number_format($package->borrow_amount,2) }}</td>
											</tr>
											<tr>
												<th>Ngày mượn</th>
												<td>{{ $package->borrow_date }}</td>
											</tr>
											<tr>
												<th>Com nhận</th>
												<td>${{ number_format($package->received,2) }}</td>
											</tr>
											<tr>
												<th>Đã rút</th>
												<td>${{ number_format($package->withdraw_complete,2) }}</td>
											</tr>
											<tr>
												<th>Trạng thái gói</th>
												<td>{{ $package->status == 1? 'ĐANG CHẠY' : ($package->status == 2 ? "ĐÃ HOÀN TẤT" : "ĐÃ HỦY") }}</td>
											</tr>
											<tr>
												
											</tr>
											<tr>
												<td colspan="2" class="text-center"> Yêu cầu rút commission</td>
											</tr>
											<tr>
												<th>Yêu cầu rút</th>
												<td>${{ number_format($package->withdraw_amount,2) }}</td>
											</tr>
											<tr>
												<th>Ngày yêu cầu rút</th>
												<td>{{ $package->withdraw_date }}</td>
											</tr>
											<tr>
												<th>Trạng thái lệnh rút</th>
												<td>{{ $package->withdraw_status == 1? 'ĐANG CHỜ' : ($package->withdraw_status == 2 ? "ĐÃ DUYỆT" : "HỦY") }}</td>
											</tr>
											@if($package->withdraw_status == 1)
											<tr>
												<th></th>
												<td>
													<a href="{{ route('admin.autotrade.approved', ['package_id' => $package->package_id]) }}" class="btn btn-success text-white" id="withdraw_process"><i class="fa fa-check fa-fw"></i>Duyệt lệnh</a>

													<a href="{{ route('admin.autotrade.cancelled', ['package_id' => $package->package_id]) }}" class="btn btn-danger text-white" id="withdraw_process"><i class="fa fa-times fa-fw"></i>Từ chối</a>
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
					<h4 class="modal-title">XÁC NHẬN!</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					Bạn xác nhận thực hiện lệnh này?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger text-white" data-dismiss="modal">Hủy</button>
					<button type="submit" class="btn btn-success text-white">Tiếp tục</button>
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