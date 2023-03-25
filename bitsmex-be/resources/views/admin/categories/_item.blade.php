@if(count($categories))
@foreach($categories as $value)
<tr>
    <td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
    <td>
        <div class="table_title">
            <a href="{{ route('admin.categories.edit', ['id' => $value->id]) }}" title="Edit this category">{!! $value->name !!}</a>
        </div>
        <ul class="table_title_actions">
            <li><a href="{{ route('admin.categories.edit', ['id' => $value->id]) }}">Edit</a></li>
            <li><a href="{{ route('admin.categories.delete', ['id' => $value->id]) }}" class="action_delete">Delete</a></li>
        </ul>
    </td>
    <td>{{ $value->post_total }}</td>
    <td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
    <td colspan="4" class="text-center">Items not found.</td>
</tr>
@endif