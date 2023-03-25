@extends('admin.app')
@section('title', 'Edit CryptoCurrency - ' . @$currency->name)
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Edit CryptoCurrency - 
			@if(!is_null($currency->logo))
			<img style="vertical-align:bottom;height:21px;" src="{{ $currency->logo }}">
			@else
			<i class="{{ $currency->icon }}" style="color:{{ $currency->color }};"></i>
			@endif
			<span class="text-success">{{ @$currency->name }}</span>
		</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2 class="text-info">Settings</h2>
						</div>
						<div class="x_content">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									{{-- <div class="form-group">
										<label><strong>Balance</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="balance" class="form-control" placeholder="Balance" value="{{ $currency->balance }}">
										</div>
									</div> --}}
									<div class="form-group">
										<label><strong>Logo</strong></label>
										<div class="input-group choose_img_lib post_single_image">
											<input type="text" name="logo" class="form-control inline fill_img_lib" placeholder="Logo" value="{{ $currency->logo }}">
											<span class="input-group-addon btn open_img_lib" gallery="false">Choose image</span>
										</div>
									</div>
									
									<div class="form-group">
										<div class="switch_toggle">
											<input type="radio" name="actived" class="switch_on" content="Active" value="1"{{ $currency->actived == 1 ? ' checked' : '' }}>
											<input type="radio" name="actived" class="switch_off" content="Not Active" value="0"{{ $currency->actived == 0 ? ' checked' : '' }}>
										</div>
									</div>
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label><strong>Phí nạp</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="deposit_fee" class="form-control" placeholder="Phí nạp" value="{{ $currency->deposit_fee }}">
										</div>
									</div>
									<div class="form-group">
										<label><strong>Phí rút</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="withdraw_fee" class="form-control" placeholder="Phí rút" value="{{ $currency->withdraw_fee }}">
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label><strong>Nạp tối thiểu</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="deposit_min" class="form-control" placeholder="Nạp tối thiểu" value="{{ $currency->deposit_min }}">
										</div>
									</div>
									<div class="form-group">
										<label><strong>Rút tối thiểu</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="withdraw_min" class="form-control" placeholder="Withdraw min" value="{{ $currency->withdraw_min }}">
										</div>
									</div>
									<div class="form-group">
										<label><strong>Rút tối đa</strong></label>
										<div class="input-group">
											<span class="input-group-addon">{{ strtoupper($currency->symbol) }}</span>
											<input type="number" step="any" name="withdraw_max" class="form-control" placeholder="Rút tối đa" value="{{ $currency->withdraw_max }}">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="x_footer">
							<button class="btn btn-primary float-left" type="submit">Lưu cài đặt</button>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@include('admin.includes.boxes.media')
@stop
@push('css')
<style>
	.x_panel{
		min-width: auto;
	}
	.crypto_show_price{
		margin-top: 5px;
	}
</style>
@endpush
@push('js')

@endpush