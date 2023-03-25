@extends('admin.app')
@section('title', 'Cập nhật phương thức - ' . $payment->name)
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cập nhật phương thức - {{ $payment->name }}</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					@csrf
					<div class="x_panel">
						<div class="x_title">
							<h2>{{ $payment->name }}</h2>
						</div>
						<div class="x_content">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label><strong>Tên</strong></label>
										<input type="text" name="name" class="form-control" value="{{ $payment->name }}" placeholder="Name">
									</div>
									<div class="form-group">
										<label><strong>Symbol</strong></label>
										<input type="text" name="symbol" class="form-control" value="{{ $payment->symbol }}" placeholder="Symbol" disabled>
									</div>
									<div class="form-group">
										<label><strong>Logo</strong></label>
										<div class="input-group choose_img_lib post_single_image">
											<input type="text" name="logo" class="form-control inline fill_img_lib" placeholder="Logo" value="{{ $payment->logo }}">
											<span class="input-group-addon btn open_img_lib" gallery="false">Choose image</span>
										</div>
									</div>
									<div class="form-group">
										<label><strong>Loại tài khoản</strong></label>
										<select name="type" id="type" class="form-control">
											<option value="payin" {{ $payment->type == 'payin' ? 'selected' : '' }}>Nhận tiền vào</option>
											<option value="payout" {{ $payment->type == 'payout' ? 'selected' : '' }}>Chuyển tiền ra</option>
										</select>
									</div>
									<div class="form-group">
										<label><strong>Hoạt động</strong></label>
										<div class="switch_toggle">
											<input type="radio" name="actived" class="switch_on" content="Enabled" value="1"{{ $payment->actived == 1 ? ' checked' : '' }}>
											<input type="radio" name="actived" class="switch_off" content="Disabled" value="0"{{ $payment->actived == 0 ? ' checked' : '' }}>
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label><strong>Số tài khoản</strong></label>
										<input type="text" name="account_number" class="form-control" value="{{ $payment->account_number }}" placeholder="Số tài khoản">
									</div>
									<div class="form-group">
										<label><strong>Tên tài khoản</strong></label>
										<input type="text" name="account_name" class="form-control" value="{{ $payment->account_name }}" placeholder="Tên tài khoản">
									</div>
									<div class="form-group">
										<label><strong>Chi nhánh</strong></label>
										<input type="text" name="account_branch" class="form-control" value="{{ $payment->account_branch }}" placeholder="Chi nhánh">
									</div>
									<div class="form-group">
										<label><strong>ID đăng nhập</strong></label>
										<input type="text" name="login_id" class="form-control" value="{{ $payment->login_id }}" placeholder="ID đăng nhập">
									</div>
									<div class="form-group">
										<label><strong>Mật khẩu đăng nhập</strong></label>
										<input type="password" name="login_password" class="form-control" value="{{ $payment->login_password }}" placeholder="Mật khẩu đăng nhập">
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label><strong>Phí nạp tiền</strong></label>
										<input type="number" step="any" name="deposit_fee" class="form-control" value="{{ $payment->deposit_fee }}" placeholder="Phí nạp tiền">
									</div>
									<div class="form-group">
										<label><strong>Phí rút tiền</strong></label>
										<input type="number" step="any" name="withdraw_fee" class="form-control" value="{{ $payment->withdraw_fee }}" placeholder="Phí rút tiền">
									</div>
									<div class="form-group">
										<label><strong>Số dư tài khoản</strong></label>
										<input type="text" class="form-control" value="{{ $payment->balance }}" disabled>
									</div>
									<div class="form-group">
										<label><strong>Cập nhật số dư</strong></label>
										<div class="switch_toggle">
											<input type="radio" name="auto_balance" class="switch_on" content="Tắt" value="1"{{ $payment->auto_balance == 1 ? ' checked' : '' }}>
											<input type="radio" name="auto_balance" class="switch_off" content="Mở" value="0"{{ $payment->auto_balance == 0 ? ' checked' : '' }}>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
		@include('admin.includes.boxes.media')
	</div>
</div>
@stop