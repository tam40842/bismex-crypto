@extends('admin.app')
@section('title', 'Thêm trang mới')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Thêm trang mới</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<div class="page_layout">
			<form action="" method="POST">
				@csrf
				<div class="left_wrapper">
					<div class="x_panel x_none x_bottom_none">
						<div class="x_content">
							<div class="form-group {{ $errors->has('post_title') ? 'has-error' : '' }}">
								<input type="text" maxlength="255" name="post_title" class="form-control input-lg {{ $action == 'add' ? 'field_name' : '' }}" placeholder="Enter title here" value="{{ isset($post->post_title) ? $post->post_title : old('post_title') }}">
							</div>
						</div>
					</div>

					<div class="x_panel x_none x_bottom_none">
						<div class="x_content">
							<div class="edit_slug_box">
								<strong>Permalink: </strong>
								<div class="sample_permalink">
									<span class="edit_post_url">{{ url('/') }}/</span>
									<span class="edit_post_name">
										<strong class="field_slug_text">{{ isset($post->slug) ? $post->slug : '' }}</strong>
										<input type="text" name="slug" class="form-control input-sm field_slug" placeholder="Permalink" value="{{ isset($post->slug) ? $post->slug : '' }}">
									</span>
									<span class="edit_post_url_last">.html</span>
									<button type="button" id="change_permalink" class="btn btn-default input-sm edit_permalink_action">Edit</button>
									<button type="button" id="submit_change_permalink" class="btn btn-default input-sm edit_permalink_action">OK</button>
									<button type="button" id="cancel_change_permalink" class="btn btn-default input-sm edit_permalink_action">Cancel</button>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>

					<div class="x_panel x_none">
						<div class="x_content">
							@include('admin.includes.boxes.editor', ['name' => 'post_content', 'content' => isset($post->post_content) ? $post->post_content : old('post_content')])
						</div>
					</div>

					<div class="x_panel x_none">
						<div class="x_content">
							<button type="submit" class="btn btn-primary">Xuất bản</button>
						</div>
					</div>

				</div>
				<div class="right_wrapper">
					
				</div>
			</form>
			@include('admin.includes.boxes.media')
		</div>
	</div>
</div>
@stop
@push('js')
<script type="text/javascript">
	$(document).on('click', '#change_permalink', function(e){
		e.preventDefault();
		$('.sample_permalink').toggleClass('active');
	});
	$(document).on('click', '#cancel_change_permalink', function(e){
		$('.sample_permalink .field_slug').val($('.sample_permalink .field_slug_text').text());
		$('.sample_permalink').removeClass('active');
	});
	$(document).on('click', '#submit_change_permalink', function(e){
		e.preventDefault();
		$('.sample_permalink .field_slug_text').text($('.sample_permalink .field_slug').val());
		$('.seo_preview .seo_link_custom').text($('.sample_permalink .field_slug').val());
		$('.sample_permalink').removeClass('active');
	});
</script>
@endpush