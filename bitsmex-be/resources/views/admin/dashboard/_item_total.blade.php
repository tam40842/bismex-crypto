@php
	use App\Http\Controllers\Vuta\Vuta;
	use App\Http\Controllers\Vuta\Statstics;
@endphp
<table class="table table-bordered table-striped" id="buy_sell_total">
	<thead>
		<tr>
			@foreach($currencies as $key => $value)
			<th>{{ strtoupper($key) }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@if(count($currencies) > 0)
		<tr class="table-info">
			@foreach($currencies as $key => $value)
			@php
				$total_value = Statstics::filter_buy_sell_total($buy_sell_filter['action'], $key, $buy_sell_filter['from'], $buy_sell_filter['to']);
			@endphp
			<td>{{ Vuta::currency_format($key, $total_value) }}</td>
			@endforeach
		</tr>
		@else
		<tr>
			<td colspan="{!! count($currencies) !!}">No result.</td>
		</tr>
		@endif
	</tbody>
</table>