@if(count($levels) > 0)
@foreach($levels as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.levels.edit', ['id' => $value->id]) }}">{{ $value->level_name }}</a>
		</div>
		<ul class="table_title_actions">
			<li><a href="{{ route('admin.levels.edit', ['id' => $value->id]) }}">Sửa</a></li>
			<li><a href="{{ route('admin.levels.delete', ['id' => $value->id]) }}" class="action_red" onclick="return confirm('Bạn muốn xóa level này ?');">Xóa</a></li>
		</ul>
	</td>
	<td>{{ $value->level }}</td>
	<td>{{ $value->percent }}%</td>
	<td>{{ $value->created_at }}</td>
	<td>{{ $value->updated_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="6" class="text-center">Chưa có level nào.</td>
</tr>
@endif
