@if(count($deposit) > 0)
@foreach($deposit as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" target="_blank" title="Edit user">{{ $value->username }}</a>
		</div>
	</td>
	<td>{{ $value->amount }} {{ $value->symbol }}</td>
	<td>{{ number_format($value->rate) }} USD</td>
	<td>{{ number_format($value->total, 2) }} USD</td>
	<td>{{ !is_null($value->stt) ? $value->stt + 1 : 'unknown' }}</td>
	<td>{!! !is_null($value->txhash) ? '<a target="_blank" href="'.$value->txhash_url.'">'.substr($value->txhash, 0, 10).'...'.substr($value->txhash, -10).'</a>' : '- - - ' !!}</td>
	<td>{!! $deposit_status[$value->status] !!}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="8" class="text-center">No results.</td>
</tr>
@endif