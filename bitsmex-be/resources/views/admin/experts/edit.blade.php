@php
    use App\Http\Controllers\Vuta\CryptoMap;
    use App\Http\Controllers\Vuta\Device;
@endphp
@extends('admin.app')
@section('title', 'Update user profile')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>{{ 'Update user profile' }}</h3>
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
									<td class="d-flex">
										<input type="text" name="username" class="form-control" value="{{ !is_null($user->username) ? $user->username : old('username') }}" placeholder="Username" required>
									</td>
								</tr>
								<tr>
									<th>First name</th>
									<td class="d-flex">
										<input type="text" name="first_name" class="form-control" value="{{ !is_null($user->first_name) ? $user->first_name : old('first_name') }}" placeholder="First name">
									</td>
								</tr>
								<tr>
									<th>Last name</th>
									<td class="d-flex">
										<input type="text" name="last_name" class="form-control" value="{{ !is_null($user->last_name) ? $user->last_name : old('last_name') }}" placeholder="Last name">
									</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>
									<input type="text" name="email" class="form-control" value="{{ !is_null($user->email) ? $user->email : old('email') }}" placeholder="Email" required value="{{ $user->email }}">
									</td>
								</tr>
								<tr>
									<th>Phone number</th>
									<td>
										<input type="text" name="phone_number" class="form-control" value="{{ !is_null($user->phone_number) ? $user->phone_number : old('phone_number') }}" placeholder="Phone">
									</td>
								</tr>
								
								<tr>
									<th>Status</th>
									<td>
										<select name="status" id="status" class="form-control w-100">
											@foreach($user_status as $key => $value)
											<option value="{{ $key }}" {{ $user->status == $key ? 'selected' : '' }}>{!! strip_tags($value) !!}</option>
											@endforeach
										</select>
									</td>
								</tr>
								
                                <tr>
									<th>Permission</th>
									<td>
                                        @php
                                            $roles = json_decode($user->roles, true);
                                            $roles = !is_null($roles) ? $roles : [];
                                        @endphp
                                        @foreach(config('roles') as $key => $value)
                                        <div class="form_group">
                                            <input type="checkbox" name="roles[]" value="{!! $key !!}" id="role_{!! $key !!}" {{ in_array($key, $roles) ? 'checked' : '' }}>
                                            <label for="role_{!! $key !!}">{!! $value !!}</label>
                                        </div>
                                        @endforeach
									</td>
								</tr>
                                <tr>
									<th>Reference link</th>
									<td>
										<input type="text" class="form-control w-100" value="{{ url('/?ref='.$user->ref_id) }}" onclick="this.select()" disabled>
									</td>
								</tr>
								<tr>
									<th>Password</th>
									<td>
										<input type="password" name="password" class="form-control w-100" autocomplete="false" autosave="false" placeholder="Leave blank if not changed">
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<table class="admin_table">
                                <tr>
                                    <th>KYC status</th>
                                    <td>
                                        <div class="switch_toggle">
											<input type="radio" name="kyc_status" class="switch_on" content="Verified" value="3"{{ $user->kyc_status == 3 ? ' checked' : '' }}>
											<input type="radio" name="kyc_status" class="switch_off" content="Not Verify" value="0"{{ $user->kyc_status == 0 ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>2FA</th>
                                    <td>
                                        <div class="switch_toggle">
											<input type="radio" name="google2fa_enable" class="switch_on" content="On" value="1"{{ $user->google2fa_enable ? ' checked' : '' }}>
											<input type="radio" name="google2fa_enable" class="switch_off" content="Off" value="0"{{ !$user->google2fa_enable ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Level</th>
                                    <td>
										<input type="text" name="level" class="form-control" value="{{ !is_null($user->level) ? $user->level : old('level') }}" placeholder="User Level" required>
                                    </td>
                                </tr>
								<tr>
                                    <th>Bỏ qua điều kiện</th>
                                    <td>
                                        <div class="switch_toggle">
											<input type="radio" name="prior_level" class="switch_on" content="On" value="1"{{ $user->prior_level ? ' checked' : '' }}>
											<input type="radio" name="prior_level" class="switch_off" content="Off" value="0"{{ !$user->prior_level ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                                @if(!is_null($sponsor))
                                <tr>
                                    <th>Sponsor ID</th>
                                    <td>
                                        <input type="text" value="{{ $sponsor->email }}" class="form-control" disabled>
                                    </td>
                                </tr>
                                @endif
                            </table>
						</div>
						<div class="col-12 text-right">
							<button class="btn btn-primary" type="submit">Save setting</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	
	<div class="page_title mt-3">
		<h3>Wallet address</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <th>Type</th>
            <th>Wallet address</th>
            <th>Destination tag</th>
            <th>Created at</th>
        </thead>
        <tbody>
            @if(count($wallets))
            @foreach($wallets as $key => $value)
            <tr>
                <td>{{ $value->symbol }}</td>
                <td>
                    <a target="_blank" href="{{ CryptoMap::transactionLink($value->symbol, $value->input_address) }}">
                        {{ $value->input_address }}
                    </a>
                </td>
                <td>{{ $value->destination_tag }}</td>
                <td>{{ $value->created_at }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4" class="text-center">No results.</td>
            </tr>
            @endif
        </tbody>
    </table>
	<div class="page_title mt-3">
		<h3>Recents login</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <th>#No</th>
            <th>IP address</th>
            <th>Location</th>
            <th>IPS</th>
            <th>Browser</th>
            <th>Os</th>
            <th>Login at</th>
        </thead>
        <tbody>
            @if(count($recent_login))
            @php
                $n = 1;
            @endphp
            @foreach($recent_login as $key => $value)
            <tr>
                <td>{{ $n++ }}</td>
                <td>{{ $value->ip }}</td>
                <td>{{ $value->location }}</td>
                <td>{{ $value->isp }}</td>
                <td>{{ $value->browser }}</td>
                <td>{{ Device::getOS($value->agent) }}</td>
                <td>{{ $value->created_at }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="7" class="text-center">No results.</td>
            </tr>
            @endif
        </tbody>
    </table>
    {!! $recent_login->appends(request()->all())->links() !!}
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
</script>
@endpush