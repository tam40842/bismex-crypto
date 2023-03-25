@if(count($commissions) > 0)
@foreach($commissions as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" target="_blank" title="Edit user">{{ $value->username }}</a>
		</div>
	</td>
	<td>${{ number_format($value->amount, 2) }}</td>
	<td>{{ $value->commission_type == 'agency' ? 'IB' : ucwords($value->commission_type) }}</td>
	<td>{{ $value->commission_type == 'agency' ? 'IB commission' : $value->message }}</td>
	{{-- <td>{{ $value->yearweek }}</td> --}}
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7" class="text-center">No results.</td>
</tr>
@endif