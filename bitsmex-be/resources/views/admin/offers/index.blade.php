@extends('admin.app')
@section('title', 'Quản lý quảng cáo')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Quản lý quảng cáo</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
        <div class="row">
            @foreach($currencies as $key => $value)
            <div class="col-sm-6 col-md grid-margin stretch-card">
				<div class="card card-statistics">
					<div class="card-body">
						<p class="text-primary text-center"><strong>Tổng số lượng {{ $value->symbol }}</strong></p>
						<div class="clearfix">
							<div class="float-left">
								<img src="{{ $value->logo }}" alt="Vietnam Đồng" style="width: 30px;">
							</div>
							<div class="float-right">
								<div class="fluid-container">
									<p class="mb-0 text-right">{{ $value->symbol }}</p>
									<h3 class="font-weight-medium text-right mb-0">{{ number_format($sum[$value->symbol], 8) }}</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
            @endforeach
            <div class="col-sm-6 col-md grid-margin stretch-card">
				<div class="card card-statistics">
					<div class="card-body">
						<p class="text-primary text-center"><strong>Tổng số lượng VND</strong></p>
						<div class="clearfix">
							<div class="float-left">
								<img src="{{ asset('contents/images/currency/vnd.png') }}" alt="Vietnam Đồng" style="width: 30px;">
							</div>
							<div class="float-right">
								<div class="fluid-container">
									<p class="mb-0 text-right">VND</p>
									<h3 class="font-weight-medium text-right mb-0">{{ number_format($sum['VND']) }}</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
		<div class="datatable">
			<div class="table_top_actions">
                <div class="table_top_actions_left">
					<form action="{{ route('admin.offers.filters') }}" method="get">
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Từ ngày</strong></label>
								<input type="date" class="form-control" name="date_from" required="required" value="{{ @$filter['date_from'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Đến ngày</strong></label>
								<input type="date" class="form-control" name="date_to" required="required" value="{{ @$filter['date_to'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Symbol</strong></label>
								<select class="form-control" name="symbol">
									<option value="">Tất cả</option>
									@foreach($currencies as $key => $currency)
									<option value="{{ $currency->symbol }}"{{ $currency->symbol==@$filter['symbol']?' selected="selected"':'' }}>{{ $currency->symbol }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label for="from"><strong>Loại</strong></label>
								<select class="form-control" name="action">
									<option value="">Tất cả</option>
									<option value="BUY"{{ @$filter['action']=='BUY'?' selected="selected"':'' }}>BUY</option>
									<option value="SELL"{{ @$filter['action']=='SELL'?' selected="selected"':'' }}>SELL</option>
								</select>
							</div>
							<div class="form-group">
								<label for="from"><strong>Trạng thái</strong></label>
								<select class="form-control" name="offer_status">
									<option value="-1" {{ @$filter['offer_status'] == '-1' ? 'selected="selected"' : '' }}>Tất cả quảng cáo</option>
									<option value="0" {{ @$filter['offer_status'] == '0' ? 'selected="selected"' : '' }}>Đang quảng cáo</option>
									<option value="1" {{ @$filter['offer_status'] == '1' ? 'selected="selected"' : '' }}>Quảng cáo hoàn tất</option>
									<option value="2" {{ @$filter['offer_status'] == '2' ? 'selected="selected"' : '' }}>Quảng cáo đã hủy</option>
								</select>
							</div>
							<div class="form-group">
								<label for="from"><strong>Hiển thị</strong></label>
								<select class="form-control" name="perpage">
									@foreach($listperpage as $key => $row)
									<option value="{{ $key }}"{{ $key==@$filter['perpage']?' selected="selected"':'' }}>{{ $row }}</option>
									@endforeach
								</select>
							</div>
							<button type="submit" class="btn btn-default mt-4">Filter</button>
						</div>
					</form>
				</div>
				<div class="table_top_actions_right">
					<img class="search_loading" src="{!! asset('contents/images/defaults/spinner.gif') !!}" alt="Search Loading">
					<div class="table_search">
						<input type="text" class="form-control table_search_text" placeholder="Keyword...">
						<span class="clear_search"><i class="glyphicon glyphicon-remove"></i></span>
						<button type="button" class="btn btn-default table_search_submit">Search</button>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="table-responsive-sm">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Mã quảng cáo</th>
							<th>Username</th>
							<th>Loại</th>
							<th>Symbol</th>
							<th>Số lượng</th>
							<th>Tỷ giá</th>
							<th>Số lượng còn lại</th>
							<th>Đã khớp</th>
							<th>Tham gia lúc</th>
						</tr>
					</thead>
					<tbody>
						@include('admin.offers._item')
					</tbody>
					<tfoot>
						<tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
							<th>Mã quảng cáo</th>
							<th>Username</th>
							<th>Loại</th>
							<th>Symbol</th>
							<th>Số lượng</th>
							<th>Tỷ giá</th>
							<th>Số lượng còn lại</th>
							<th>Đã khớp</th>
							<th>Tham gia lúc</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
					
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $offers->count() . ' of ' . $offers->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $offers->appends(request()->all())->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
@stop
@push('js')
<script type="text/javascript">
    table_search($('.table_search_submit'), "{{ route('admin.offers.search') }}");

    $(document).on('change', '#offer_status', function() {
        var offer_status = $(this).find('option:selected').val();
        if(window.location.href.indexOf("?") > -1) {
            window.location.href= window.location.href + '&offer_status='+offer_status;
        } else {
            window.location.href= '?offer_status='+offer_status;
        }
	    return false;
    })
</script>
@endpush