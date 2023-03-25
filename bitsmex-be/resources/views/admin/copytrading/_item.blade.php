@if(count($supperTrade) > 0)
@foreach($supperTrade as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/copytrading/edit/' . $value->id) !!}">{{ $value->name }}</a>
		</div>
	</td>
	<td>{{ $value->email }}</td>
	<td>${{ number_format($value->amount_min,2) }}</td>
	<td>{{ $value->fee }}%</td>
	<td>{{ $value->profit }}%</td>
	<td>{!! $supper_trader_status[$value->status] !!}</td>
	<td>{!! $value->created_at !!}</td>
</tr>
@endforeach
@else
<tr class="text-center">
	<td colspan="7">Items not found.</td>
</tr>
@endif
