@extends('admin.app')
@section('title', 'Cài đặt thông báo nạp tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cài đặt thông báo nạp tiền</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Cài đặt thông báo nạp tiền</h2>
                        </div>
                        <div class="x_content">
                            <table class="table admin_table">
                                <tr>
                                    <th>Tắt/Mở thông báo</th>
                                    <td>
                                        <div class="switch_toggle">
                                            <input type="radio" name="is_website_notice_deposit" class="switch_on" content="Mở" value="1"{{ $setting['is_website_notice_deposit'] ? ' checked' : '' }}>
                                            <input type="radio" name="is_website_notice_deposit" class="switch_off" content="Tắt" value="0"{{ !$setting['is_website_notice_deposit'] ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <label for="notice_message"><strong>Nội dung thông báo</strong></label>
                                @include('admin.includes.boxes.editor', ['name' => 'website_notice_deposit', 'content' => isset($setting['website_notice_deposit']) ? $setting['website_notice_deposit'] : old('website_notice_deposit')])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">
                        Lưu cài đặt
                    </button>
                </div>
            </div>
        </form>
	</div>
</div>
@include('admin.includes.boxes.media')
@stop