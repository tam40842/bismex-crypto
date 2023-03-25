@php
	use App\Http\Controllers\Vuta\Vuta;
@endphp
@if(count($advantages) > 0)
@foreach($advantages as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.advantages.edit', ['id' => $value->id]) }}" title="Edit">
				<img src="{{ $value->image }}" alt="{{ $value->name }}" style="background: #000;">
			</a>
		</div>
	</td>
	<td>
		<div class="table_title text-uppercase">
			<a href="{{ route('admin.advantages.edit', ['id' => $value->id]) }}" title="Edit">{{ $value->name }}</a>
		</div>
	</td>
	
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="3">Không có dữ liệu.</td>
</tr>
@endif