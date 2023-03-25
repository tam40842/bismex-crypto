@if(count($rich) > 0)
@foreach($rich as $key => $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>{{ ($key + 1) }}</td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/users/edit/' . $value->id) !!}">{{ $value->username }}</a>
		</div>
	</td>
	<td>{{ $value->email }}</td>
	<td>${{ number_format($value->total_balance, 2) }}</td>
	<td>{!! $status[$value->status] !!}</td>
	<td>{!! $value->created_at !!}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7">Items not found.</td>
</tr>
@endif
