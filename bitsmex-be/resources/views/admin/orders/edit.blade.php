@extends('admin.app')
@section('title', 'Transaction detail')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Transaction detail - <span class="text-success text-uppercase">#{{ @$order->orderid }}</span></h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info text-uppercase">Account information</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th>Username</th>
												<td>{{ $user->username }}</td>
											</tr>
											<tr>
												<th>First name</th>
												<td>{{ $user->first_name }}</td>
											</tr>
											<tr>
												<th>Last name</th>
												<td>{{ $user->last_name }}</td>
											</tr>
											<tr>
												<th>Identity number</th>
												<td><strong class="text-danger">{{ $user->identity_number }}</strong></td>
											</tr>
											<tr>
												<th>Phone number</th>
												<td>{{ $user->phone_number }}</td>
											</tr>
											<tr>
												<th>Email</th>
												<td>{{ $user->email }}</td>
											</tr>
											<tr>
												<th>Verify status</th>
												<td>{!! $user->kyc_status ? '<span class="badge badge-success">Đã xác minh</span>' : '<span class="badge badge-danger">Chưa xác minh</span>' !!}</td>
											</tr>
											<tr>
												<th>Balance availabled</th>
												<td>{{ number_format($user->live_balance, 2) }} USD</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info text-uppercase">Transaction detail</h2>
								</div>
								<div class="x_content">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<td>Order ID</td>
												<td><strong class="text-info">{{ $order->orderid }}</strong></td>
											</tr>
											<tr>
												<td>Type</td>
												<td>{{ ($order->action == 'BUY') ? 'BUY' : 'SELL' }}</td>
											</tr>
											<tr>
												<td>Market name</td>
												<td>{{ $order->market_name }}</td>
											</tr>
											<tr>
												<td>Amount</td>
												<td>{{ number_format($order->amount) }} USD</td>
											</tr>
											<tr>
												<td>Profit percent</td>
												<td>{{ $order->profit_percent }}%</td>
											</tr>
											<tr>
												<td>Profit</td>
												<td>{{ ($order->status == 1) ? '$'.($order->amount + ($order->amount * $order->profit_percent / 100)) : '---' }}</td>
											</tr>
											<tr>
												<td>Round</td>
												<td>{{ $order->round }}</td>
											</tr>
											<tr>
												<td>Created date</td>
												<td>{{ $order->created_at }}</td>
											</tr>
											<tr>
												<td>Status</td>
												<td>{!! $order_status[$order->status] !!}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@stop
@push('css')

@endpush
@push('js')

@endpush