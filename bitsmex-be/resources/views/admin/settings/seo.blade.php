@extends('admin.app')
@section('title', 'Cấu hình SEO')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cấu hình SEO</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<table class="admin_table">
				<tr>
					<th>Link Telegram</th>
					<td>
						<input type="text" name="site_telegram" class="form-control" value="{{ $setting['site_telegram'] }}" placeholder="Telegram Link">
					</td>
				</tr>
				<tr>
					<th>Link Twitter</th>
					<td>
						<input type="text" name="site_twitter" class="form-control" value="{{ $setting['site_twitter'] }}" placeholder="Twitter Link">
					</td>
				</tr>
				<tr>
					<th>Link Facebook</th>
					<td>
						<input type="text" name="site_facebook" class="form-control" value="{{ $setting['site_facebook'] }}" placeholder="Facebook Link">
					</td>
				</tr>
				<tr>
					<th>Tawk.to ID</th>
					<td>
						<input type="text" name="tawk_to_id" class="form-control" value="{{ $setting['tawk_to_id'] }}" placeholder="Tawk.to ID">
					</td>
				</tr>
				<tr>
					<th>Google Analytics script</th>
					<td>
						<textarea name="google_analytics" id="google_analytics" rows="3" class="form-control" placeholder="Mã theo dõi google analytics">{{ $setting['google_analytics'] }}</textarea>
					</td>
                </tr>
                <tr>
					<th>Default site Description</th>
					<td>
                        <textarea name="google_analytics" id="site_description" rows="3" class="form-control" placeholder="Description mặc định">{{ $setting['site_description'] }}</textarea>
					</td>
                </tr>
                <tr>
					<th>Keywords</th>
					<td>
						<input type="text" name="site_keywords" class="form-control" value="{{ $setting['site_keywords'] }}" placeholder="Các từ khóa cách nhau dấu phẩy">
					</td>
				</tr>
                <tr>
					<th>Default site thumbnail</th>
					<td>
						<div class="choose_img_lib post_single_image">
							<div class="input-group">
								<input type="text" name="site_default_thumbnail" class="form-control fill_img_lib" placeholder="Thumbnail mặc định" value="{{ old('site_default_thumbnail') }}">
								<span class="input-group-btn">
									<button type="button" class="btn btn-secondary open_img_lib" gallery="false">Choose image</button>
								</span>
							</div>
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