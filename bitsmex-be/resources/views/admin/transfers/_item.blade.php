@if(count($transfers) > 0)
@foreach($transfers as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.transfers.edit', ['transfers_id' => $value->transfer_id]) }}" title="Edit order">#{{ $value->transfer_id }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" target="_blank" title="Edit user">{{ $value->sender }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.edit', ['id' => $value->recipient_id]) }}" target="_blank" title="Edit user">{{ $value->receiver }}</a>
		</div>
	</td>
	<td>{{ number_format($value->amount) }}</td>
	<td>{{ $value->fee }}</td>
	<td>{{ number_format($value->total) }}</td>
	<td>{!! $transfers_status[$value->status] !!}</td>
	<td>{!! $status_admin_setup[$value->admin_setup] !!}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="9" class="text-center">Chưa có giao dịch nạp nào.</td>
</tr>
@endif