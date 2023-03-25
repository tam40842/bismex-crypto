@if(count($offers) > 0)
@foreach($offers as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.offers.edit', ['offer_id' => $value->offer_id]) }}">{{ $value->offer_id }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/users/edit/' . $value->userid) !!}">{{ $value->username }}</a>
		</div>
	</td>
	<td>{!! $value->action == 'BUY' ? '<strong class="text-success">Mua</strong>' : '<strong class="text-danger">Bán</strong>' !!}</td>
	<td>{{ $value->symbol }}</td>
	<td><strong>{{ number_format($value->amount, 8) }}</strong></td>
	<td>{{ number_format($value->price) }}</td>
	<td>{{ number_format($value->amount_remain, 8) }}</td>
	<td>{{ 100 - round(($value->amount_remain / $value->amount) * 100, 2) }}%</td>
	<td>{!! $status[$value->status] !!}</td>
	<td>{!! $value->created_at !!}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7">Items not found.</td>
</tr>
@endif
