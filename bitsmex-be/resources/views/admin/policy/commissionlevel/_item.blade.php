@if(count($commissionlevel) > 0)
@foreach($commissionlevel as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.policy.commissionlevel.edit', ['id' => $value->id]) }}" title="Edit">{{ $value->level_name }}</a>
		</div>
	</td>
	<td>{{ $value->level_number }}</td>
	<td>{{ $value->percent }}%</td>
    <td>{!! $commission_level_status[$value->actived] !!}</td>
	<td>{{ $value->created_at }}</td>
	<td>{{ $value->updated_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="10" class="text-center">Không hoa hồng hệ thống nào.</td>
</tr>
@endif