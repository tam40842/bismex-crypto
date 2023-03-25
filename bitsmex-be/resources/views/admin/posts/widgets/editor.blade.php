<div class="x_panel x_none">
	<div class="x_content">
		@include('admin.includes.boxes.editor', ['name' => 'post_content', 'content' => isset($post->post_content) ? $post->post_content : old('post_content')])
	</div>
</div>