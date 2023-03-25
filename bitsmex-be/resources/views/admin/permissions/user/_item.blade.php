@if(count($user) > 0)
@foreach($user as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.permissions.user.edit', ['id' => $value->id]) }}" title="{{ __('Edit partner') }}">{{ $value->username }}</a>
		</div>
		<ul class="table_title_actions">
			<li>
				<a href="{{ route('admin.permissions.user.edit', ['id' => $value->id]) }}">{{ __('Edit') }}</a>
			</li>
			<li>
				<a class="text-danger" href="{{ route('admin.permissions.user.delete', ['id' => $value->id]) }}" onclick="return confirm('{{ __('Are you sure to delete the user permission ?') }}')">{{ __('Delete') }}</a>
			</li>
		</ul>
	</td>
    <td>
    @if(@$value->Role()->name)
    <span class="badge badge-primary">{{ $value->Role()->name }}</span>
    @endif
    </td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="2">{{ __('Items not found.') }}</td>
</tr>
@endif