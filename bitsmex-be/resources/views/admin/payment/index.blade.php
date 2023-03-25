@extends('admin.app')
@section('title', 'Phương thức thanh toán')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Phương thức thanh toán</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="datatable">
			<div class="table-responsive-sm">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Logo</th>
							<th>Tên</th>
							<th>Số tài khoản</th>
							<th>Tên tài khoản</th>
							<th>Chi nhánh</th>
							<th>Số dư</th>
							<th>Loại tài khoản</th>
							<th>Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						@if(!is_null($payments))
						@foreach($payments as $value)
						<tr>
							<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
							<td>
								<div style="width:41px;height:41px;">
									<div class="img_wrapper">
										<div class="img_show">
											<div class="img_thumbnail">
												<div class="img_centered">
													<img src="{{ $value->logo }}" alt="{{ $value->name }}">
												</div>
											</div>
										</div>
									</div>
								</div>
							</td>
							<td>
								<a href="{{ route('admin.payments.edit', ['id' => $value->id]) }}">
									{{ $value->name }}
								</a>
							</td>
							<td>{{ $value->account_number }}</td>
							<td>{{ $value->account_name }}</td>
							<td>{{ $value->account_branch }}</td>
							<td>{{ number_format($value->balance) }} VND</td>
							<td>{!! $value->type == 'payin' ? '<strong class="text-success"><i class="fa fa-long-arrow-down fa-fw"></i> Nhận tiền vào</strong>' : '<strong class="text-danger"><i class="fa fa-long-arrow-up fa-fw"></i> Chuyển tiền ra</strong>' !!}</td>
							<td>{!! $value->actived ? '<span class="badge badge-success"><i class="fa fa-check fa-fw"></i> Hoạt động</span>' : '<span class="badge badge-danger"><i class="fa fa-times fa-fw"></i> Không hoạt động</span>' !!}</td>
						</tr>
						@endforeach
						@else
						<tr>
							<td colspan="8">Items not found.</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $payments->count() . ' of ' . $payments->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $payments->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@stop