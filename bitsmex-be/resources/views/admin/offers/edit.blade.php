@extends('admin.app')
@section('title', 'Chi tiết quảng cáo')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Chi tiết quảng cáo</h3>
		{{ $offer->offer_id }}
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
				<form action="" method="POST">
					@csrf
					<div class="x_panel">
						<div class="x_title">
							<h2>Người quảng cáo</h2>
						</div>
						<div class="x_content">
							<div class="kyc_user_profile table-responsive">
								<table class="table table-bordered">
									<tr>
										<th>Username</th>
										<td>{{ $user->username }}</td>
									</tr>
									<tr>
										<th>Họ tên</th>
										<td>{{ $user->first_name.' '.$user->last_name }}</td>
									</tr>
									<tr>
										<th>Số trên giấy tờ</th>
										<td><strong class="text-danger">{{ $user->identity_number }}</strong></td>
									</tr>
									<tr>
										<th>Số điện thoại</th>
										<td>{{ $user->phone_number }}</td>
									</tr>
									<tr>
										<th>Địa chỉ Email</th>
										<td>{{ $user->email }}</td>
									</tr>
									<tr>
										<th>Trạng thái xác minh</th>
										<td>{!! $user->kyc_status ? '<span class="badge badge-success">Đã xác minh</span>' : '<span class="badge badge-danger">Chưa xác minh</span>' !!}</td>
									</tr>
									<tr>
										<th>Số dư hiện tại</th>
										<td>{{ number_format($user->UserBalance()->{$offer->symbol}, 8) }} {{ $offer->symbol }}</td>
									</tr>
									<tr>
										<th>Số dư VND</th>
										<td>{{ number_format($user->UserBalance()->VND) }} VND</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Chi tiết quảng cáo</h2>
					</div>
					<div class="x_content">
                        <table class="table table-bordered">
                            <tr>
                                <th>Mã quảng cáo</th>
                                <td>{{ $offer->offer_id }}</td>
                            </tr>
                            <tr>
                                <th>Loại quảng cáo</th>
                                <td>{{ $action_type[$offer->action] }}</td>
                            </tr>
                            <tr>
                                <th>Loại tiền</th>
                                <td>{{ $offer->symbol }}</td>
                            </tr>
                            <tr>
                                <th>Số lượng</th>
                                <td>{{ number_format($offer->amount, 8) }} {{ $offer->symbol }}</td>
                            </tr>
                            <tr>
                                <th>Tỷ giá</th>
                                <td>{{ number_format($offer->price) }} VND</td>
                            </tr>
                            <tr>
                                <th>Phí giao dịch</th>
                                <td>{{ number_format($offer->fee) }} VND</td>
                            </tr>
                            <tr>
                                <th>Tổng tiền</th>
                                <td>{{ number_format($offer->price_has_fee) }} VND</td>
                            </tr>
                            <tr>
                                <th>Số lượng còn lại</th>
                                <td>{{ number_format($offer->amount_remain, 8) }} {{ $offer->symbol }}</td>
                            </tr>
                            <tr>
                                <th>Đã khớp</th>
                                <td>{{ 100 - round($offer->amount_remain / $offer->amount * 100, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>IP Người tạo</th>
                                <td>{{ $offer->ip }}</td>
                            </tr>
                            <tr>
                                <th>Tạo quảng cáo lúc</th>
                                <td>{{ $offer->created_at }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái quảng cáo</th>
                                <td>{!! $offer_status[$offer->status] !!}</td>
                            </tr>
                        </table>
					</div>
				</div>
			</div>
        </div>
        <div class="page_title">
            <h3>Danh sách giao dịch</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <th>Mã giao dịch</th>
                    <th>Người giao dịch</th>
                    <th>Số lượng {{ $offer->action == 'BUY' ? 'bán' : 'mua' }}</th>
                    <th>Thành tiền</th>
                    <th>Giao dịch lúc</th>
                </thead>
                <tbody>
                    @if(count($order_list))
                    @php
                        $amount = 0;
                        $total = 0;
                    @endphp
                    @foreach($order_list as $key => $value)
                    @php
                        $amount += $value->amount;
                        $total += $value->total;
                    @endphp
                    <tr>
                        <td>{{ $value->orderid }}</td>
                        <td>{{ $value->username }}</td>
                        <td>{{ number_format($value->amount, 8) }} {{ $value->symbol }}</td>
                        <td>{{ number_format($value->total) }} VND</td>
                        <td>{{ $value->created_at }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="font-weight-bold text-danger">{{ number_format($amount, 8) }} {{ $value->symbol }}</td>
                        <td class="font-weight-bold text-danger">{{ number_format($total) }} VND</td>
                        <td></td>
                    </tr>
                    @else
                    <tr>
                        <td class="text-center" colspan="5">Không có giao dịch nào cho quảng cáo này.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {!! $order_list->links() !!}
	</div>
</div>
@stop