@extends('admin.app')
@section('title', 'Bulk Mail')
@section('content')
<div class="content_wrapper">
	<div class="page_title p-3">
		<h3>Bulk Mail</h3>
	</div>
	@include('admin.includes.boxes.notify')
    <div>
        <form action="" method="POST">
            @csrf
            <div class="x_panel">
                <div class="x_content">
                    <div class="row">
                        <div class="col-12">
                            <table class="table admin_table">
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <input type="radio" name="type" id="email" value="email" checked> <label for="email" class="mr-5">Đến Email</label>
                                        <input type="radio" name="type" id="all" value="all"> <label for="all">Đến tất cả</label>
                                    </td>
                                </tr>
                                <tr id="type">
                                    <th>Email nhận</th>
                                    <td>
                                        <input type="text" name="email" class="form-control w-100" placeholder="Mỗi email cách nhau dấu phẩy" data-role="tagsinput" value="{{ old('message') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>
                                        <input type="text" name="subject" class="form-control w-100" placeholder="Subject" required value="{{ old('subject') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Message</th>
                                    <td>
                                        @include('admin.includes.boxes.editor', ['name' => 'message', 'content' => old('message')])
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('admin.includes.boxes.media')
@endsection
@push('css')
<link href="{{ asset('contents/admin/css/bootstrap-tagsinput.css') }}" rel="stylesheet">
@endpush
@push('js')
<script src="{{ asset('contents/admin/js/bootstrap-tagsinput.js') }}"></script>
<script>
    $(document).on('change', 'input[name="type"]', function() {
        var type = $('#type');
        var o = $(this);
        if(o.val() == 'all') {
            type.addClass('d-none');
        } else {
            type.removeClass('d-none');
        }
    })
</script>
@endpush