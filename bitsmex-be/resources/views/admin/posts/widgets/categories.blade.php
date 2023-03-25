<div class="x_panel">
	<div class="x_title">
		<h2>{{ __('Categories') }}</h2>
	</div>
	<div class="card-body categories_list">
		@php
			$post_categories = isset($post->post_categories) ? json_decode($post->post_categories, true) : [];
		@endphp
		@foreach($categories as $key => $value)
			<div class="form-group">
				<label class="checkbox">
					<input type="checkbox" value="{{ $value->id }}" name="categories[]" {{ in_array($value->id, $post_categories) ? 'checked' : '' }}>
					{{ $value->name }}
					<span class="checkmark"></span>
				</label>
			</div>
		@endforeach
	</div>
</div>