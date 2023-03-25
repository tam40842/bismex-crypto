@extends('admin.app')
@section('title', 'Xác minh tài liệu')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Xác minh tài liệu</h3>
		{{ $user->username }}
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				<form action="" method="POST">
					@csrf
					<div class="x_panel">
						<div class="x_title">
							<h2>Thông tin tài khoản</h2>
						</div>
						<div class="x_content">
							<div class="kyc_user_profile table-responsive">
								<table class="table table-bordered">
									<tr>
										<th>Username</th>
										<td>{{ $user->username }}</td>
									</tr>
									<tr>
										<th>Họ tên</th>
										<td>{{ $user->first_name }} {{ $user->last_name }}</td>
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
										<th>Trạng thái KYC</th>
										<td>
											<select class="form-control" name="status">
												<option value="1"{{ $kyc->status == 1 ? ' selected' : '' }}>Chờ xác nhận</option>
												<option value="2"{{ $kyc->status == 2 ? ' selected' : '' }}>Chấp nhận KYC</option>
												<option value="3"{{ $kyc->status == 3 ? ' selected' : '' }}>Từ chối KYC</option>
											</select>
											@if ($errors->has('status'))
												<span class="invalid-feedback" role="alert">
													<strong>* {{ $errors->first('status') }}</strong>
												</span>
											@endif
										</td>
									</tr>
									<tr id="reason"></tr>
									@if($user->kyc_status != 2)
									<tr>
										<th></th>
										<td><button type="submit" class="btn btn-primary">Save Changes</button></td>
									</tr>
									@endif
								</table>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Hình ảnh xác minh</h2>
					</div>
					<div class="x_content">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="form-group">
									<label><strong>Ảnh Selfie</strong></label>
									<input type="text" class="form-control" placeholder="Selfie picture" value="{!! isset($kyc->selfie) ? $kyc->selfie : '' !!}" disabled>
									<div class="airdrop_profile_img">
										<a href="{!! isset($kyc->selfie) ? $kyc->selfie : '' !!}" target="_blank">
											<img src="{!! isset($kyc->selfie) ? $kyc->selfie : '' !!}" alt="">
										</a>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								
								<div class="form-group">
									<label><strong>Giấy tờ (mặt trước)</strong></label>
									<input type="text" name="identity_frontend" class="form-control" placeholder="In front of passport" value="{!! isset($kyc->identity_frontend) ? $kyc->identity_frontend : '' !!}" disabled>
									<div class="airdrop_profile_img">
										<a href="{!! isset($kyc->identity_frontend) ? $kyc->identity_frontend : '' !!}" target="_blank">
											<img src="{!! isset($kyc->identity_frontend) ? $kyc->identity_frontend : '' !!}" alt="">
										</a>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="form-group">
									<label><strong>Giấy tờ (mặt sau)</strong></label>
									<input type="text" class="form-control" placeholder="The back of passport" value="{!! isset($kyc->identity_backend) ? $kyc->identity_backend : '' !!}" disabled>
									<div class="airdrop_profile_img">
										<a href="{!! isset($kyc->identity_backend) ? $kyc->identity_backend : '' !!}" target="_blank">
											<img src="{!! isset($kyc->identity_backend) ? $kyc->identity_backend : '' !!}" alt="">
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@push('css')
<style>
	.kyc_user_avatar{
		display:block;
		margin:0 auto;
		width:100px;
		height:100px;
		margin-bottom:10px;
		border-radius:200px;
		overflow:hidden;
	}
	.airdrop_profile_img{
		display: block;
		margin-top: 15px;
		width: 100%;
	}
	.airdrop_profile_img img{
		display: block;
		margin: 0 auto;
		max-width: 100%;
	}
</style>
@endpush
@push('js')
<script>
	$(document).on('change', '[name="status"]', function() {
		var o = $(this);
		var status_id = o.find('option:selected').val();
		var html = '';
		if(status_id == 3) {
			html += `<th>Lý do từ chối</th>
					<td><textarea class="form-control" name="reason" rows="5"></textarea></td>`;
		} else {
			html = '';
		}
		$('#reason').html(html);
	});
</script>
@endpush