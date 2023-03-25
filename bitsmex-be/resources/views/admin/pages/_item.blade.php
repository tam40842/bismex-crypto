@php
	use App\Http\Controllers\Vuta\Vuta;
@endphp
@if(count($posts) > 0)
@foreach($posts as $value)
<tr>
    <td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
    <td>
        <div class="table_title">
            <a href="{{ route('admin.pages.edit', ['id' => $value->id]) }}">
                {{ Vuta::_substr($value->post_title, 50) }}
            </a>
        </div>
        <ul class="table_title_actions">
            <li><a href="{{ route('admin.pages.edit', ['id' => $value->id]) }}">Sửa</a></li>
            <!-- <li><a target="_blank" href="{{ route('home.page', ['slug' => $value->slug]) }}" class="text-info">Xem</a></li> -->
            <li><a target="_blank" href="#" class="text-info">Xem</a></li>
            <li><a href="{{ route('admin.pages.delete', ['id' => $value->id]) }}" class="text-danger" onclick="return confirm('Bạn muốn xóa trang này?');">Xóa</a></li>
        </ul>
    </td>
    <td>
        <a target="_blank" href="{{ route('admin.users.edit', ['id' => $value->post_author]) }}"><strong>{{ $value->username }}</strong></a>
    </td>
    <td>{{ $value->post_status }}</td>
    <td>{{ number_format($value->view_count) }}</td>
    <td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
    <td colspan="6">Chưa có trang nào</td>
</tr>
@endif