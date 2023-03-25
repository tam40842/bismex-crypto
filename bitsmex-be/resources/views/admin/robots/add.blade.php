@extends('admin.app')
@section('title', 'Thêm Robot')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Thêm Robot</h3>
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
										<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
									</td>
								</tr>
								<tr>
                                    <th><label for="from">Số tiền tối thiểu</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="min" class="form-control" value="{{ old('min') }}" required>
										</div>
									</td>
                                </tr>
								<tr>
									<th><label for="from">Số tiền tối đa</label></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="max" class="form-control" value="{{ old('max') }}" required>
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
											<input type="text" name="interest" class="form-control" value="{{ old('interest') }}" required>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="">Số tháng</label></th>
									<td>
										<div class="input-group">
											<input type="text" name="month" class="form-control" value="{{ old('month') }}" required>
										</div>
									</td>
								</tr>
								{{-- <tr>
									<th>Hình ảnh</th>
									<td>
										<div class="choose_img_lib post_single_image">
											<div class="input-group">
												<input type="text" name="image" class="form-control fill_img_lib" placeholder="" value="{{ old('image') }}">
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
											<input type="radio" name="actived" class="switch_on" content="Mở" value="1">
											<input type="radio" name="actived" class="switch_off" content="Tắt" value="0">
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
</div>
@include('admin.includes.boxes.media')
@stop