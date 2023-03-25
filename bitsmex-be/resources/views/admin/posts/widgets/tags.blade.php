@php
	$tags = isset($post->post_tags) ? array_filter(explode(',', $post->post_tags)) : [];
@endphp
<div class="x_panel">
	<div class="x_title">
		<h2>Tags</h2>
	</div>
	<div class="x_content">
		<div class="post_add_new_tag">
			<input type="hidden" name="post_tags" id="post_tag" value="{{ isset($post->post_tags) ? $post->post_tags : '' }}">
			<div class="input-group">
				<input type="text" class="form-control" id="post_add_new_tag_name" placeholder="Tags...">
				<span class="input-group-btn">
					<button class="btn btn-default" id="post_add_new_tag_submit" type="button">Add</button>
				</span>
			</div>
			<div class="form-group bottom_none">
				<i>Separate tags with commas (,).</i>
			</div>
			<div class="form-group">
				<ul class="post_show_tag_add_new">
					@if(isset($post->post_tags) && !empty($tags))
                        @foreach($tags as $tag)
                        <li><i class="fa fa-times"></i> {{ $tag }}</li>
                        @endforeach
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>