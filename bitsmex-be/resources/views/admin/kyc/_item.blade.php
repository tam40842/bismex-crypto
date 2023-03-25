@if(count($kycs) > 0)
@foreach($kycs as $value)
<tr>
	<td><input type="checkbox" value="{{ $value->id }}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.verifing.edit', ['id' => $value->verify_id]) }}" title="Edit KYC">{{ $value->email }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.verifing.edit', ['id' => $value->verify_id]) }}" target="_blank" title="Edit user">{{ $value->first_name }} {{ $value->last_name }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.verifing.edit', ['id' => $value->verify_id]) }}" target="_blank" title="Edit user">{{ $value->identity_number }}</a>
		</div>
	</td>
	<td>{!! $status[$value->kyc_status] !!}</td>
	<td>{{ $value->verify_created_at }}</td>
	<td>{{ $value->verify_updated_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="8" class="text-center">Items not found.</td>
</tr>
@endif