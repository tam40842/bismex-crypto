<div class="x_panel x_none x_bottom_none">
	<div class="x_content">
		<div class="edit_slug_box">
			<strong>Permalink: </strong>
			<div class="sample_permalink">
				<span class="edit_post_url">{{ url('/') }}/.../</span>
				<span class="edit_post_name">
					<strong class="field_slug_text">{{ isset($post->slug) ? $post->slug : '---' }}</strong>
					<input type="text" name="slug"
						class="form-control input-sm field_slug" placeholder="Permalink"
						value="{{ isset($post->slug) ? $post->slug : '' }}">
				</span>
				<span class="edit_post_url_last">.html</span>
				<button type="button" id="change_permalink"
					class="btn btn-default input-sm edit_permalink_action">Edit</button>
				<button type="button" id="submit_change_permalink"
					class="btn btn-default input-sm edit_permalink_action">OK</button>
				<button type="button" id="cancel_change_permalink"
					class="btn btn-default input-sm  edit_permalink_action">Cancel</button>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<style>
	.edit_slug_box{
		float: left;
		margin-top: 5px;
		margin-bottom: 10px;
	}
	.edit_slug_box strong{
		float: left;
		margin-top: 10px;
		font-weight: 500;
	}
	.sample_permalink{
		float: left;
		margin-left: 5px;
	}
	.sample_permalink a{
		float: left;
	}
	.sample_permalink .edit_post_url{
		float: left;
		margin-top: 10px;
	}
	.sample_permalink .edit_post_name{
		float: left;
	}
	.sample_permalink .edit_post_url_last{
		float: left;
		margin-top: 10px;
	}
	.sample_permalink .edit_post_name .input-sm{
		float: left;
		margin-top: 5px;
		width: 350px;
		height: 24px;
		font-size: 13px;
		padding: 1px 5px 3px 5px;
	}
	.sample_permalink #change_permalink{
		display: block;
		height: 20px!important;
	}
	.sample_permalink.active #change_permalink{
		display: none;
	}
	.sample_permalink #submit_change_permalink{
		display: none;
	}
	.sample_permalink.active #submit_change_permalink{
		display: block;
		height: 20px!important;
	}
	.sample_permalink #cancel_change_permalink{
		display: none;
	}
	.sample_permalink.active #cancel_change_permalink{
		display: block;
		height: 20px!important;
	}
	.sample_permalink .edit_post_name input{
		display: none;
	}
	.sample_permalink.active .edit_post_name input{
		display: block;
		height: 20px!important;
	}
	.sample_permalink .field_slug_text{
		white-space: nowrap;
		max-width: 350px;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		min-height: 15px;
	}
	.sample_permalink.active .field_slug_text{
		display: none;
	}
	.edit_permalink_action{
		float: left;
		height: 24px;
		padding: 1px 10px;
		margin-top: 5px;
		margin-left: 5px;
		border-radius: 3px;
		font-size: 12px;
	}
	@media screen and (max-width: 767px){
		.sample_permalink{
			margin-left: 0;
			width: 100%;
		}
		.sample_permalink .edit_post_name .input-sm{
			width: auto;
		}
	}
</style>
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