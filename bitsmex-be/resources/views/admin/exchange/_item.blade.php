@if(count($exchange) > 0)
@foreach($exchange as $value)
<tr>
	<td><input type="checkbox" value="{{ $value->id }}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.exchange.edit', ['id' => $value->id]) }}" title="Edit order">
				{{ $value->username }}
			</a>
		</div>
	</td>
	<td class="font-weight-bold">{{ !$value->is_swap ? $value->symbol : 'USD' }} <=> {{ $value->is_swap ? $value->symbol : 'USD' }} </td>
	<td>{{ $value->amount }}</td>
	<td>{{ $value->rate }}</td>
	<td>{{ $value->total }}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7" class="text-center">Chưa có giao dịch nào.</td>
</tr>
@endif