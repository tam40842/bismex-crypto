@extends('admin.app')
@section('title', 'Reply user ticket')
@section('content')
<div class="content_wrapper">
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <form action="" method="POST" class="deposit-form" enctype="multipart/form-data">
            @csrf
            <div class="x_panel">
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-8 col-sm-12 col-xs-12">
                            <h3>Subject: {{ $tickets[0]->subject }}</h3>
                            @foreach(@$tickets as $key => $value)
                            <div class="p-3 border border-secondary mb-2">
                                <strong>{{ $value->username }} - {{ $value->created_at }}</strong>
                                <div class="mt-2">{!! $value->message !!}</div>
                            </div>
                            @endforeach
                            <div class="form-group">
                                <label for="my-input"><strong>Reply</strong></label>
                                @include('admin.includes.boxes.editor', ['name' => 'message', 'message' =>
                                isset($post->message) ? $post->message : old('message')])
                                <small class="text-dark">{{ $errors->first('message') }}</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary deposit_btn">Reply</button>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Hình ảnh ticket</h2>
                                </div>
                                <div class="x_content">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label><strong>Ảnh đính kèm</strong></label>
                                                <div class="airdrop_profile_img">
                                                    <a href="{!! isset($value->image) ? $value->image : '' !!}" target="_blank">
                                                        <img src="{!! isset($value->image) ? $value->image : '' !!}" alt="">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('admin.includes.boxes.media')
@endsection
@push('js')
<script type="text/javascript">
$(document).on('click', '#change_permalink', function(e) {
    e.preventDefault();
    $('.sample_permalink').toggleClass('active');
});
$(document).on('click', '#cancel_change_permalink', function(e) {
    $('.sample_permalink .field_slug').val($('.sample_permalink .field_slug_text').text());
    $('.sample_permalink').removeClass('active');
});
$(document).on('click', '#submit_change_permalink', function(e) {
    e.preventDefault();
    $('.sample_permalink .field_slug_text').text($('.sample_permalink .field_slug').val());
    $('.seo_preview .seo_link_custom').text($('.sample_permalink .field_slug').val());
    $('.sample_permalink').removeClass('active');
});
</script>
@endpush