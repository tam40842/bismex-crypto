@extends('admin.app')
@section('title', 'Cấu hình căn bản')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cấu hình căn bản</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<table class="admin_table">
				<tr>
					<th>Site Title</th>
					<td>
						<input type="text" name="title_website" class="form-control" value="{{ $settings['title_website'] }}" placeholder="Site Title">
					</td>
				</tr>
				<tr>
					<th>Email Address</th>
					<td>
						<input type="text" name="site_email" class="form-control" value="{{ $settings['site_email'] }}" placeholder="Email Address">
					</td>
				</tr>
				<tr>
					<th>Support Phone</th>
					<td>
						<input type="text" name="site_phone" class="form-control" value="{{ $settings['site_phone'] }}" placeholder="Support phone number">
					</td>
				</tr>
				<tr>
					<th>Backup password</th>
					<td>
						<input type="password" name="password_backup" class="form-control" value="" placeholder="Mật khẩu phụ">
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<a href="{{ route('admin.profit_reset_time') }}" class="btn btn-success" onclick="return confirm('Bạn muốn reset lại lợi nhuận sàn ?')">Reset Profit</a>
						<p><small>{{ !is_null($settings['profit_reset_time']) ? 'Reset lần cuối lúc: '.$settings['profit_reset_time'] : '' }}</small></p>
					</td>
				</tr>
				<tr>
					<th>Logo Default</th>
					<td>
						<div class="choose_img_lib setting_img">
							<div class="form-group">
								<div class="img_wrapper setting_img">
									<div class="img_show">
										<div class="img_thumbnail">
											<div class="img_centered">
												<img class="show_img_lib" src="{!! !empty($settings['site_logo']) ? $settings['site_logo'] : asset('contents/images/defaults/no-image.jpg') !!}" alt="Featured Image">
											</div>
										</div>
										<div class="remove_featured_image">
											<button><i class="dashicons dashicons-no-alt"></i></button>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="form-group bottom_five">
								<a href="javascript:void(0);" class="open_img_lib" gallery="false">Set logo</a>
							</div>
							<input type="hidden" class="fill_img_lib" name="site_logo" value="{!! !empty($settings['site_logo']) ? $settings['site_logo'] : '' !!}">
						</div>
					</td>
				</tr>
				<tr>
					<th>Favicon</th>
					<td>
						<div class="choose_img_lib setting_img">
							<div class="form-group">
								<div class="img_wrapper setting_img">
									<div class="img_show">
										<div class="img_thumbnail">
											<div class="img_centered">
												<img class="show_img_lib" src="{!! !empty($settings['site_favicon']) ? $settings['site_favicon'] : asset('contents/images/defaults/no-image.jpg') !!}" alt="Featured Image">
											</div>
										</div>
										<div class="remove_featured_image">
											<button><i class="dashicons dashicons-no-alt"></i></button>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="form-group bottom_five">
								<a href="javascript:void(0);" class="open_img_lib" gallery="false">Set favicon</a>
							</div>
							<input type="hidden" class="fill_img_lib" name="site_favicon" value="{!! !empty($settings['site_favicon']) ? $settings['site_favicon'] : '' !!}">
						</div>
					</td>
				</tr>
				<tr>
					<th colspan="2"><button type="submit" class="btn btn-primary">Lưu cài đặt</button></th>
				</tr>
			</table>
		</form>
	</div>
</div>
@include('admin.includes.boxes.media')
@stop
@push('css')
<style>
	.setting_img .img_wrapper{
		float: left;
		width: 100px;
		height: 100px;
	}
	.setting_img .remove_featured_image{
		line-height: 84px;
	}
	.setting_img .open_img_lib{
		font-weight: 500;
		text-decoration: underline;
	}
	.remove_featured_image button{
		margin-top: 35px;
	}
</style>
@endpush
@push('js')

@endpush