@extends('admin.app')
@section('title', 'Cài đặt thông báo')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cài đặt thông báo</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Cài đặt thông báo</h2>
                        </div>
                        <div class="x_content">
                            <table class="table admin_table">
                                <tr>
                                    <th>Tắt/Mở thông báo</th>
                                    <td>
                                        <div class="switch_toggle">
                                            <input type="radio" name="is_website_notice" class="switch_on" content="Mở" value="1"{{ $setting['is_website_notice'] ? ' checked' : '' }}>
                                            <input type="radio" name="is_website_notice" class="switch_off" content="Tắt" value="0"{{ !$setting['is_website_notice'] ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <label for="notice_message"><strong>Nội dung thông báo</strong></label>
                                @include('admin.includes.boxes.editor', ['name' => 'website_notice', 'content' => isset($setting['website_notice']) ? $setting['website_notice'] : old('website_notice')])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Cài đặt bảo trì</h2>
                        </div>
                        <div class="x_content">
                            <table class="table admin_table">
                                <tr>
                                    <th>Tắt/Mở bảo trì</th>
                                    <td>
                                        <div class="switch_toggle">
                                            <input type="radio" name="is_maintenance" class="switch_on" content="Mở" value="1"{{ $setting['is_maintenance'] ? ' checked' : '' }}>
                                            <input type="radio" name="is_maintenance" class="switch_off" content="Tắt" value="0"{{ !$setting['is_maintenance'] ? ' checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <label for="notice_message"><strong>Nội dung bảo trì</strong></label>
                                @include('admin.includes.boxes.editor', ['name' => 'maintenance_content', 'content' => isset($setting['maintenance_content']) ? $setting['maintenance_content'] : old('maintenance_content')])
                            </div>
                            <table class="table admin_table">
                                <tr>
                                    <th>Bảo trì đến</th>
                                    <td>
                                        <input type="datetime-local" class="form-control" name="maintenance_expired" value="{{ date('Y-m-d\TH:i', strtotime($setting['maintenance_expired'])) }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngoại trừ IP</th>
                                    <td>
                                    @php
                                        $maintenance_allowed_ip = json_decode($setting['maintenance_allowed_ip'], true);
                                    @endphp
                                    <input type="text" class="form-control" name="maintenance_allowed_ip" value="{{ implode(', ', $maintenance_allowed_ip) }}">
                                    </td>
                                </tr>
                            </table>
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