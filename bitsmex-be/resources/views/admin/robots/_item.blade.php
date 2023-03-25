@if(count($robots) > 0)
@foreach($robots as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.robots.edit', ['id' => $value->id]) }}">{{ $value->name }}</a>
		</div>
		{{-- <ul class="table_title_actions">
			<li><a href="{{ route('admin.robots.edit', ['id' => $value->id]) }}">Sửa</a></li>
			<li><a href="{{ route('admin.robots.delete', ['id' => $value->id]) }}" class="action_red" onclick="return confirm('Bạn muốn xóa robot này ?');">Xóa</a></li>
		</ul> --}}
	</td>
	<td>{{ number_format($value->min,0) }} $</td>
	<td>{{ number_format($value->max,0) }} $</td>
	<td>{{ $value->bonus }} %</td>
	<td>{{ $value->fee }} %</td>
	<td>{{ $value->interest }} %</td>
	<th><span class="badge badge-success">{{ $value->month }} Month</span></th>
	<td>{!! $status[$value->actived] !!}</td>
	<td>{{ date('d-m-Y', strtotime($value->updated_at)) }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="9" class="text-center">Chưa có Robot nào.</td>
</tr>
@endif
