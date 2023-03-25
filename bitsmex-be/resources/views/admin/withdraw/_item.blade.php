@if(count($withdraw) > 0)
@foreach($withdraw as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.withdraw.edit', ['withdraw_id' => $value->withdraw_id]) }}" title="Edit order">#{{ $value->withdraw_id }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" target="_blank" title="Edit user">{{ $value->username }}</a>
		</div>
	</td>
	<td>{{ $value->symbol }}</td>
	<td>{{ $value->symbol == 'VNDC' ? number_format($value->amount) : number_format($value->amount, 8) }}</td>
	<td>{{ $value->symbol == 'VNDC' ? number_format($value->fee) : number_format($value->fee, 2) }}</td>
	<td>{{ $value->symbol == 'VNDC' ? number_format($value->total) : number_format($value->total, 8) }}</td>
	<td>{!! $withdraw_status[$value->status] !!}</td>
	<td>{{ $value->created_at }}</td>
	<td>{{ $value->updated_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="10" class="text-center">Không có lệnh rút tiền nào.</td>
</tr>
@endif