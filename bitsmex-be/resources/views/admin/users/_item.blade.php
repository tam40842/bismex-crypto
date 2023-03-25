@if(count($users) > 0)
@foreach($users as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/users/edit/' . $value->id) !!}">{{ $value->username }}</a>
		</div>
		<ul class="table_title_actions">
			<li><a href="{{ route('admin.users.edit', ['id' => $value->id]) }}">Edit</a></li>
			<li>
				@if($value->status != 2)
				<a href="{{ route('admin.users.banned', ['id' => $value->id]) }}" class="action_red" onclick="return confirm('You want banned this account ?');">Banned</a>
				@else
				<a href="{{ route('admin.users.banned', ['id' => $value->id]) }}" class="action_red" onclick="return confirm('You want unban this account ?');">Unban</a>
				@endif
			</li>
		</ul>
	</td>
	<td>{{ $value->email }}</td>
	<td>{!! $value->kyc_status ? '<i class="fa fa-check text-success"><i>' : '<i class="fa fa-times text-danger"><i>' !!}</td>
	<td>{!! $status[$value->status] !!}</td>
	<td>${{ number_format($value->total_volume(), 2) }}</td>
	<td>${{ number_format($value->live_balance, 2) }}</td>
	<td>{!! $status_admin_setup[$value->admin_setup] !!}</td>
	<td>
		@foreach(json_decode($value->roles) as $role)
			<span class="badge badge-dark">{{ ($role == 'admin') ? 'Admin' : 'Member' }}</span>
		@endforeach
	</td>
	<td>{!! $value->created_at !!}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7">Items not found.</td>
</tr>
@endif
