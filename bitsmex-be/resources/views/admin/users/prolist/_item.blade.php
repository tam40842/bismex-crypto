@if(count($prolist) > 0)
@foreach($prolist as $key => $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>{{ ($key + 1) }}</td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/users/edit/' . $value->id) !!}">{{ $value->username }}</a>
		</div>
	</td>
	<td>${{ number_format($value->live_balance, 2) }}</td>
	<td class="text-success font-weight-bold">${{ number_format($value->wintotal, 2) }}</td>
	<td class="text-danger font-weight-bold">${{ number_format($value->losetotal, 2) }}</td>
	@if($value->wintotal - $value->losetotal >= 0)
	<td class="text-success font-weight-bold">${{ number_format($value->wintotal - $value->losetotal, 2) }}</td>
	@else
	<td class="text-danger font-weight-bold">-${{ number_format(abs($value->wintotal - $value->losetotal), 2) }}</td>
	@endif
</tr>
@endforeach
@else
<tr>
	<td class="text-center" colspan="7">Items not found.</td>
</tr>
@endif
