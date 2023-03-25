@extends('admin.app')
@section('title', $action == 'edit' ? 'Cập nhật tài khoản' : 'Thêm mới tài khoản')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>{{ $action == 'edit' ? 'Cập nhật tài khoản' : 'Thêm mới tài khoản' }}</h3>
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
									<th>Username</th>
									<td>
										<input type="text" name="username" class="form-control" value="{{ !is_null(@$user->username) ? @$user->username : old('username') }}" placeholder="Username" required>
									</td>
								</tr>
								{{-- <tr>
									<th>First name</th>
									<td>
										<input type="text" name="first_name" class="form-control" value="{{ !is_null(@$user->first_name) ? @$user->first_name : old('first_name') }}" placeholder="First name" required>
									</td>
								</tr>
								<tr>
									<th>Last name</th>
									<td>
										<input type="text" name="last_name" class="form-control" value="{{ !is_null(@$user->last_name) ? @$user->last_name : old('last_name') }}" placeholder="Last name" required>
									</td>
								</tr> --}}
								<tr>
									<th>Email</th>
									<td>
										<input type="text" name="email" class="form-control" value="{{ !is_null(@$user->email) ? @$user->email : old('email') }}" placeholder="Email" required value="{{ @$user->email }}">
									</td>
								</tr>
								<tr>
									<th>Phone number</th>
									<td>
										<input type="text" name="phone_number" class="form-control" value="{{ !is_null(@$user->phone_number) ? @$user->phone_number : old('phone_number') }}" placeholder="Phone" required value="{{ @$user->phone_number }}">
									</td>
								</tr>
								
								<tr>
									<th>User status</th>
									<td>
										<select name="status" id="status" class="form-control w-100">
											@foreach(@$user_status as $key => $value)
											<option value="{{ $key }}" {{ @$user->status == $key ? 'selected' : '' }}>{!! strip_tags($value) !!}</option>
											@endforeach
										</select>
									</td>
								</tr>
								<tr>
									<th>Password</th>
									<td>
										<input type="password" name="password" class="form-control" autocomplete="false" autosave="false" placeholder="Để rỗng nếu không thay đổi">
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="roles"><strong>Role</strong></label>
								@foreach(config('roles') as $key => $value)
								<div class="form_group">
									<input type="checkbox" name="roles[]" value="{!! $key !!}" id="role_{!! $key !!}" {{ !is_null(old('roles')) && in_array($key, old('roles')) ? 'checked' : '' }}>
									<label for="role_{!! $key !!}">{!! $value !!}</label>
								</div>
								@endforeach
							</div>
							<div class="form-group">
								<label for="user_setup"><strong>User setup</strong></label>
								<div class="alert alert-warning" role="alert">
									Thành viên setup sẽ không được nạp, rút, chuyển tiền nội bộ với thành viên thật.
								</div>
								<div class="switch_toggle">
									<input type="radio" id="show" name="admin_setup" class="switch_on" content="On" value="1">
									<input type="radio" id="hidden" name="admin_setup" class="switch_off" content="Off" value="0">
								</div>
							</div>
							<div class="form-group menu">
								<label for="live_balance"><strong>Live user</strong></label>
								<input type="text" name="live_balance" class="form-control" placeholder="Live balance user setup" value="{{ old('live_balance') }}">
							</div>
						</div>
						<div class="col pt-3">
							<button class="btn btn-primary" type="submit">Lưu cài đặt</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@stop
@push('css')
<style>
	.generate_password_input{
		display: none;
		width: 25em;
		position: relative;
	}
	.generate_password_input .field_password{
		padding-right: 40px;
	}
	.generate_password_input .show_hide_pass{
		position: absolute;
		top: 0;
		right: -25px;
		width: 35px;
		height: 28px;
		text-align: center;
		cursor: pointer;
		padding-top: 3px;
	}
	.remove_featured_image button{
		margin-top: 35px;
	}
</style>
@endpush
@push('js')
<script type="text/javascript">
	$(document).on('click', '.generate_password', function(){
		if($(this).closest('.generate_password_area').find('.generate_password_input').is(':visible')){
			$(this).text('Change Password');
			$(this).closest('.generate_password_area').find('.generate_password_input').css('display', 'none');
			$(this).closest('.generate_password_area').find('.field_password').val('');
		}else{
			$(this).text('Cancel');
			$(this).closest('.generate_password_area').find('.generate_password_input').css('display', 'block');
			$(this).closest('.generate_password_area').find('.field_password').val('').focus();
		}
	});
	$(document).on('click', '.show_hide_pass', function(){
		if($(this).closest('.generate_password_input').find('.field_password').attr('type') == 'password'){
			$(this).closest('.generate_password_input').find('.field_password').attr('type', 'text');
			$(this).find('.dashicons').removeClass('dashicons-visibility');
			$(this).find('.dashicons').addClass('dashicons-hidden');
		}else{
			$(this).closest('.generate_password_input').find('.field_password').attr('type', 'password');
			$(this).find('.dashicons').removeClass('dashicons-hidden');
			$(this).find('.dashicons').addClass('dashicons-visibility');
		}
	});
	$(document).ready(function() {
		$('.menu').css('display', 'none');
	});
	$('#show').click(function() {
      $('.menu').show();
    });
	
	$('#hidden').click(function() {
      $('.menu').hide();
    });
</script>
@endpush