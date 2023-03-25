@extends('admin.app')
@section('title', $action == 'edit' ? 'Update copy trader' : 'Add copy trader')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>{{ $action == 'edit' ? 'Update copy trader' : 'Add copy trader' }}</h3>
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
									<th>Email</th>
									<td>
										<input type="text" name="email" class="form-control" value="{{ !is_null(@$superTrade->email) ? @$superTrade->email : old('email') }}" placeholder="Email" required>
									</td>
								</tr>
								<tr>
									<th>Display name</th>
									<td>
										<input type="text" name="name" class="form-control" value="{{ !is_null(@$superTrade->name) ? @$superTrade->name : old('name') }}" placeholder="Display name" required>
									</td>
								</tr>
								<tr>
									<th>Minimum investment amount</th>
									<td>
										<input type="text" name="amount_min" class="form-control" value="{{ !is_null(@$superTrade->amount_min) ? @$superTrade->amount_min : old('amount_min') }}" placeholder="Min amount" required value="{{ @$superTrade->amount_min }}">
									</td>
								</tr>
								<tr>
									<th>Performance fee</th>
									<td>
										<input type="text" name="fee" class="form-control" value="{{ !is_null(@$superTrade->fee) ? @$superTrade->fee : old('fee') }}" placeholder="Performance fee" required value="{{ @$superTrade->fee }}">
									</td>
								</tr>
								<tr>
									<th>Profit</th>
									<td>
										<input type="text" name="profit" class="form-control" value="{{ !is_null(@$superTrade->profit) ? @$superTrade->profit : old('profit') }}" placeholder="Performance profit" required value="{{ @$superTrade->profit }}">
									</td>
								</tr>
								@if($action == 'edit')
								<tr>
									<th>Status</th>
									<td>
										<select class="form-control" name="status" id="">
											@if($superTrade->status == 0)
											<option {{ !$superTrade->status ? 'selected' : '' }} value="0">Pedding</option>
											@endif
											<option {{ $superTrade->status == 1 ? 'selected' : '' }} value="1">Actived</option>
											<option {{ $superTrade->status == 2 ? 'selected' : '' }} value="2">Cancelled</option>
										</select>
									</td>
								</tr>
								@endif
							</table>
							<button class="btn btn-primary" type="submit">Save</button>
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
</script>
@endpush