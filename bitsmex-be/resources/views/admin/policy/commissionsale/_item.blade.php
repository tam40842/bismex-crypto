@if(count($commissionsale) > 0)
@foreach($commissionsale as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.policy.commissionsale.edit', ['id' => $value->id]) }}" title="Edit">{{ $value->level_name }}</a>
		</div>
	</td>
    <td>{!! $commission_sale_status[$value->actived] !!}</td>
	<td>{{ $value->created_at }}</td>
	<td>{{ $value->updated_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="10" class="text-center">Không hoa hồng hệ thống nào.</td>
</tr>
@endif