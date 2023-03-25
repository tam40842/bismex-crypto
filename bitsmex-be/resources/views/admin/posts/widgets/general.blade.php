@php
    use App\Http\Controllers\Vuta\Status;
    $post_status = Status::post_status();
@endphp
<div class="x_panel">
	<div class="x_title">
		<h2>Publish</h2>
	</div>
	<div class="x_content">
		<div class="post_general">
			<div class="form-group">
				<div class="post_general_heading">
					<i class="dashicons dashicons-post-status"></i>
					<span>Status: </span>
					<strong id="post_status_text">
                        {!! isset($post->post_status) ? $post_status[$post->post_status] : 'Publish' !!}
					</strong>
					<a href="javascript:void(0);"> Edit</a>
				</div>
				<div class="post_general_content">
					<div class="form-group">
						<select name="post_status" class="form-control">
                            @foreach($post_status as $key => $value)
							<option value="{{ $key }}" {{ isset($post->post_status) && $post->post_status == $key ? ' selected' : '' }}>{!! strip_tags($value) !!}</option>
                            @endforeach
						</select>
						<button type="button" id="submit_change_status" class="btn btn-default">OK</button>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="post_general_heading">
					<i class="dashicons dashicons-calendar"></i>
					<span>Publish: </span>
					<strong id="post_time_text">immediately</strong>
					<a href="javascript:void(0);"> Edit</a>
				</div>
				<div class="post_general_content">
					<div class="form-group">
						@if(isset($post->published_at))
						@include('admin.includes.boxes.datetime', ['datetime' => $post->published_at])
						@else
						@include('admin.includes.boxes.datetime')
						@endif
					</div>
					<div class="form-group">
						<button type="button" id="submit_change_time" class="btn btn-default">OK</button>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="post_general_heading publish_seo_score">
					<i class="dashicons dashicons-chart-bar"></i>
					<span>Readability: </span>
					<strong>Needs improvement</strong>
				</div>
			</div>
			<div class="form-group">
				<div class="post_general_heading publish_seo_status">
					<i class="dashicons dashicons-chart-bar"></i>
					<span>SEO: </span>
					<strong>Not available</strong>
				</div>
			</div>
		</div>
	</div>
	<div class="x_footer">
		<div class="form-group">
			<button type="submit" name="post_submit" value="draft" class="btn btn-default float-left" disabled>Save Draft</button>
			<button type="submit" name="post_submit" value="publish" class="btn btn-primary float-right">Publish</button>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<style>
	.post_general select{
		display: inline-block;
		vertical-align: top;
		width: auto;
	}
	.post_general_heading i{
		color: #82878c;
		vertical-align: text-bottom;
	}
	.post_general_heading a{
		text-decoration: underline;
	}
	.post_general_content{
		display: none;
		margin-top: 10px;
	}
	.post_general_content.active{
		display: block;
	}
	.post_general_content .form-group{
		margin-bottom: 5px;
	}
	.post_general_content .form-group:last-child{
		margin-bottom: 0;
	}
	.post_password_area{
		display: none;
		margin-top: 5px;
		margin-bottom: 10px;
	}
</style>
<script type="text/javascript">
	$(document).on('click', '.post_general_heading a', function(e){
		e.preventDefault();
		$(this).closest('.form-group').find('.post_general_content').addClass('active');
		$(this).hide();
	});

	$(document).on('click', '#submit_change_status', function(e){
		e.preventDefault();
		$('#post_status_text').text($(this).closest('.form-group').find('select option:selected').text());
		$(this).closest('.post_general_content').removeClass('active');
		$(this).closest('.post_general_content').closest('.form-group').find('.post_general_heading a').show();
	});

	$(document).on('click', '#submit_change_visibility', function(e){
		e.preventDefault();
		var visibility_val = $(this).closest('.post_general_content').find('.post_visibility:checked').attr('id');
		$('#post_visibility_text').text($('label[for="' + visibility_val + '"]').text());
		$(this).closest('.post_general_content').removeClass('active');
		$(this).closest('.post_general_content').closest('.form-group').find('.post_general_heading a').show();
	});

	$(document).on('click', '#submit_change_time', function(e){
		e.preventDefault();
		var time_val = $('select[name="datetime[year]"]').val() + '-' + $('select[name="datetime[month]"]').val() + '-' + $('select[name="datetime[day]"]').val() + '@' + $('select[name="datetime[hour]"]').val() + ':' + $('select[name="datetime[minute]"]').val();
		if(time_val.trim() != ''){
			$('#post_time_text').text(time_val);
		}else{
			$('#post_time_text').text('immediately');
		}
		$(this).closest('.post_general_content').removeClass('active');
		$(this).closest('.post_general_content').closest('.form-group').find('.post_general_heading a').show();
	});

	$(document).on('change', '.post_visibility', function(e){
		e.preventDefault();
		var visibility_val = $(this).val();
		if(visibility_val == 'password'){
			$('.post_password_area').show();
		}else{
			$('.post_password_area').hide();
		}
	});
</script>