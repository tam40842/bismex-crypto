@if(count($histories) > 0)
@foreach($histories as $value)
<tr>
	<td><input type="checkbox" value="{{ $value->id }}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<small>
				<a href="{{ route('admin.lastround.byround', ['round' => $value->round_id]) }}">{{ $value->round_id }}</a>
			</small>
		</div>
	</td>
	
	<td>{{ $value->marketname }}</td>
	<td>{{ $value->open }}</td>
	<td>{{ $value->high }}</td>
	<td>{{ $value->low }}</td>
	<td>{{ $value->close }}</td>
	<td>{!! $value->result == 'SELL' ? '<span class="badge badge-danger">'.$value->result.'</span>' : '<span class="badge badge-success">'.$value->result.'</span>' !!}</td>
	<td>{{ $value->username }}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="10" class="text-center">Items not found.</td>
</tr>
@endif