@if(count($posts))
@foreach($posts as $key => $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div style="width:41px;height:41px;">
			<div class="img_wrapper">
				<div class="img_show">
					<div class="img_thumbnail">
						<div class="img_centered">
							<img src="{{ $value->post_img }}" alt="{{ $value->post_title }}">
						</div>
					</div>
				</div>
			</div>
		</div>
	</td>
	<td style="width: 200px;">
		<div class="table_title">
			<a href="{{ route('admin.posts.edit', ['id' => $value->id]) }}" title="Edit currency">{!! $value->post_title !!}</a>
		</div>
		<ul class="table_title_actions">
			<li>
				<a href="{{ route('admin.posts.edit', ['id' => $value->id]) }}">Edit</a>
			</li>
			<li>
				<a href="{{ route('admin.posts.delete', ['id' => $value->id]) }}" class="action_delete">Delete</a>
			</li>
		</ul>
	</td>
	<td>{!! $post_status[$value->post_status] !!}</td>
	<td>
		<a href="{{ route('admin.users.edit', ['id' => $value->post_author]) }}" target="_blank">
			{{ $value->first_name.' '.$value->last_name }}
		</a>
	</td>
	<td>
		{{ $value->view_count }}
	</td>
	<td>
		{{ $value->created_at }}
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="7" class="text-center">Items not found.</td>
</tr>
@endif