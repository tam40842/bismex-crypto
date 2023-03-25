@if($errors->any())
@foreach ($errors->all() as $error)
<div class="page_option">
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		{!! $error !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
@endforeach
@endif
@if(Session::has('notify_type') && Session::has('notify_content'))
@php
	$notify_type = Session::get('notify_type');
	if(Session::get('notify_type') == 'error'){
		$notify_type = 'danger';
	}
@endphp
<div class="page_option">
	<div class="alert alert-{!! $notify_type !!} alert-dismissible fade show" role="alert">
		{!! Session::get('notify_content') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
@endif
@if(Session::has('alert_success'))
<div class="page_option">
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		{!! Session::get('alert_success') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
@endif
@if(Session::has('alert_error'))
<div class="page_option">
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		{!! Session::get('alert_error') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
@endif