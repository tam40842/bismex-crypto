@extends('admin.app')
@section('title', 'Level commission')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Level commission</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="post">
			@csrf
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<table class="admin_table">
								<tr>
									<th>Level name</th>
									<td>
										<input type="text" name="level_name" class="form-control" value="{{ isset($level->level_name) ? $level->level_name : old('level_name') }}" placeholder="Level name" required>
									</td>
								</tr>
								<tr>
									<th>Số tầng</th>
									<td>
										<input type="number" name="level" class="form-control" value="{{ isset($level->level) ? $level->level : old('level') }}" placeholder="Số tầng" required>
									</td>
								</tr>
								<tr>
									<th>Hoa hồng</th>
									<td>
										<div class="input-group">
										<input type="number" step="any" name="percent" class="form-control" value="{{ isset($level->percent) ? $level->percent : old('percent') }}" placeholder="Hoa hồng" required>
										<span class="input-group-addon">%</span>
										</div>
									</td>
								</tr>
								<tr>
									<th></th>
									<td>
										<button class="btn btn-primary" type="submit">Lưu cài đặt</button>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@stop