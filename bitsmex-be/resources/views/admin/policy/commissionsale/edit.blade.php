@extends('admin.app')
@section('title', 'Sửa level')
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>Sửa level</h3>
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
                                            placeholder="Nhập tên hòa hồng" required value="{{ $commissionsale->level_name }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Điều kiện</th>
                                    @foreach(@json_decode($commissionsale->floors) as $key => $value)
                                    <td>
                                        <input type="text" name="floors[]" class="form-control w-100"
                                            placeholder="Điều kiện n F1" value="{{ $value }}">
                                        <h1><span class="input-group-addon">tổng doanh số F{{ $key + 1 }}</span></h1>
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        <div class="switch_toggle">
											<input type="radio" name="actived" class="switch_on" content="Actived" value="1"{{ $commissionsale->actived == 1 ? ' checked' : '' }}>
											<input type="radio" name="actived" class="switch_off" content="Inactive" value="0"{{ $commissionsale->actived == 0 ? ' checked' : '' }}>
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