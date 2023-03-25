@if(count($role) > 0)
@foreach($role as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<small>
				<a href="{{ route('admin.permissions.role.edit', ['id' => $value->id]) }}" title="Edit order">{{ $value->name }}</a>
			</small>
		</div>
		@if($value->slug != 'supper-admin')
		<ul class="table_title_actions">
			<li>
				<a class="text-danger" href="{{ route('admin.permissions.role.delete', ['id' => $value->id]) }}" onclick="return confirm('{{ __('Are you sure to delete the permission ?') }}')">{{ __('Delete') }}</a>
			</li>
		</ul>
		@endif
	</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td class="text-center" colspan="3">{{ __('Items not found.') }}</td>
</tr>
@endif