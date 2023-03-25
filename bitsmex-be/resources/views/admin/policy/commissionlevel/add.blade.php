@extends('admin.app')
@section('title', 'Thêm level')
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>Thêm Level</h3>
    </div>
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <div class="x_panel">
            <div class="x_title">
                <h2 class="text-info"><span class="text-uppercase">Thông tin level</h2>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-10 col-12">
                        <form action="" method="post">
                            @csrf
                            <table class="table admin_table">
                                <tr>
                                    <th>Level name</th>
                                    <td>
                                        <input type="text" name="level_name" class="form-control w-100"
                                            placeholder="Nhập tên hòa hồng" required value="{{ old('level_name') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Level</th>
                                    <td>
                                        <input type="text" name="level_number" class="form-control w-100"
                                            placeholder="Nhập level hoa hồng" required value="{{ old('level_number') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phần trăm</th>
                                    <td>
                                        <input type="text" name="percent" class="form-control w-100"
                                            placeholder="Nhập phần trăm hoa hồng" required
                                            value="{{ old('percent') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Điều kiện</th>
                                    <td>
                                        <input type="text" name="f1_count" class="form-control w-100"
                                            placeholder="Điều kiện n F1" required
                                            value="{{ old('f1_count') }}">
                                        <h1><span class="input-group-addon">Điều kiện n F1</span></h1>
                                    </td>
                                    <td>
                                        <input type="text" name="personal_volume" class="form-control w-100"
                                            placeholder="Giao dịch cá nhân/Tuần" required
                                            value="{{ old('personal_volume') }}">
                                            <h1><span class="input-group-addon">Giao dịch cá nhân/Tuần</span></h1>
                                    </td>
                                    <td>
                                        <input type="text" name="f1_volume" class="form-control w-100"
                                            placeholder="Giao dịch F1/Tuần" required
                                            value="{{ old('f1_volume') }}">
                                            <h1><span class="input-group-addon">Giao dịch F1/Tuần</span></h1>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        <div class="switch_toggle">
											<input type="radio" name="actived" class="switch_on" content="Actived" value="1" checked>
											<input type="radio" name="actived" class="switch_off" content="Inactive" value="0">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <input type="submit" value="Xác nhận" class="btn btn-primary withdraw_btn">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop