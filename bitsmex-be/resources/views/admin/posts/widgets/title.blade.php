<div class="x_panel x_none x_bottom_none">
	<div class="x_content">
		<div class="form-group {{ $errors->has('post_title') ? 'has-error' : '' }}">
			<input type="text" name="post_title" value="{{ isset ($post->post_title) ? $post->post_title : '' }}" class="form-control w-100 {{ !isset($post->post_title) ? 'field_name' : '' }}" placeholder="The name categories" required>
		</div>
	</div>
</div>