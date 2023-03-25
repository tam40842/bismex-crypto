@php
	use App\Http\Controllers\Vuta\Vuta;
@endphp
@extends('admin.app')
@section('title', __('Dashboard'))
@section('content')
<div class="content_wrapper">
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="row">
			<div class="col-md-12 grid-margin">
				<div class="card card-statistics">
					<div class="card-body">
						<h5 class="card-title"><strong>Site Tracking</strong></h5>
						<canvas id="tracking_chart" style="width:100%;height:200px;"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop