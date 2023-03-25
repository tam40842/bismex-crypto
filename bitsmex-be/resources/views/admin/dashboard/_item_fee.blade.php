@php
	use App\Http\Controllers\Vuta\Vuta;
	use App\Http\Controllers\Vuta\Statstics;
@endphp
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			@foreach($fiats as $key => $value)
			<th>{{ strtoupper($key) }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<tr class="table-success">
			@foreach($fiats as $key => $value)
			@php
				$total_value = Statstics::filter_fee_total($fee_filter['action'], strtolower($key), $fee_filter['from'], $fee_filter['to']);
			@endphp
			<td>{{ Vuta::currency_format($key, $total_value) }}</td>
			@endforeach
		</tr>
	</tbody>
</table>