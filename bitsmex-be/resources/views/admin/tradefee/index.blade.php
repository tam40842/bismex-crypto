@extends('admin.app')
@section('title', 'Hoa hồng hệ thống')
@section('content')
<div class="content_wrapper">
    @include('admin.includes.boxes.notify')
    <div class="">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label for="from"><strong>Lợi nhuận sàn</strong></label>
                    <input type="text" class="js-range-slider hour_change" name="system_win_percent"
                        value="{{ $system_win_percent }}" />
                </div>
                <div class="form-group">
                    <label for="from"><strong>Phần trăm autotrade</strong></label>
                    <input type="text" class="js-range-slider hour_change" name="autotrade_percent"
                        value="{{ $autotrade_percent }}" />
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-md-6 col-12">
                <label for="from"><strong>Giới hạn giao dịch</strong></label>
                <div class="form-group">
                    <div class="input-group mb-1">
                        <span class="input-group-addon">Min</span>
                        <input type="number" id="transfer_limit" name="trade_min" class="form-control" placeholder="Giao dịch tối thiểu" value="{{ $trade_min }}">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">Max</span>
                        <input type="number" id="transfer_limit" name="trade_max" class="form-control" placeholder="Giao dịch tối đa" value="{{ $trade_max }}">
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label for="from"><strong>Giới hạn chuyển tiền trong hệ thống</strong></label>
                    <div class="input-group mb-1">
                        <span class="input-group-addon">Min</span>
                        <input type="number" id="transfer_limit" name="transfer_min" class="form-control" placeholder="Mức thấp nhất" value="{{ $transfer_min }}">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">Max</span>
                        <input type="number" id="transfer_limit" name="transfer_max" class="form-control" placeholder="Mức cao nhất" value="{{ $transfer_max }}">
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="datatable">
            <div class="table_top_actions">
                @if(count($tradefee) > 0)
                <div class="row">
                    <div class="col-md-6 col-12">
                        <table class="table admin_table">
                            <th class="p-3">Phí Giao Dịch</th>
                            @foreach(@$tradefee as $key => $value)
                            @if($key < 12) <tr>
                                <td>{{ $value->hour }}h - {{ $value->hour+1 }}h</td>
                                <td>
                                    <input type="text" class="js-range-slider hour_change" name="{{ $value->hour }}" value="{{ $value->value }}" />
                                </td>
                                </tr>
                                @endif
                                @endforeach
                        </table>
                    </div>
                    <div class="col-md-6 col-12">
                        <table class="table admin_table">
                            <th class="p-3">Phí Giao Dịch</th>
                            @foreach(@$tradefee as $key => $value)
                            @if($key >= 12)
                            <tr>
                                <td>{{ $value->hour }}h - {{ $value->hour+1 }}h</td>
                                <td>
                                    <input type="text" class="js-range-slider hour_change" name="{{ $value->hour }}" value="{{ $value->value }}" />
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @stop
    @push('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.0/css/ion.rangeSlider.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
    .table_user_avatar .img_wrapper {
        float: left;
        width: 32px;
        height: 32px;
    }

    .table_user_roles {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    </style>
    @endpush
    @push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.0/js/ion.rangeSlider.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript">
    $(".js-range-slider").ionRangeSlider({
        min: 0,
        max: 100,
        prefix: "% ",
    });
    $(".transfer_min").ionRangeSlider({
        min: 0,
        max: 100,
        prefix: "$ ",
    });
    $("#trade_range").ionRangeSlider({
        type: "double",
        min: 1,
        max: 1000000,
        prefix: "$",
        prettify_separator: ",",
        onFinish: function (data) {
            $.post("{{ route('admin.tradefee.range') }}", {
                key: 'trade_range',
                value: data.from+';'+data.to,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(data) {
                toastr.success(data.message, 'Success')
            })
            .fail(function(err, textStatus, errorThrown) {
                var error_text = JSON.parse(err.responseText);
                $.each(error_text.errors, function(key, value) {
                    toastr.error(value, 'Error')
                });
            });
        },
    });

    $(document).on('change', '#transfer_limit', function() {
        var key = $(this).attr('name');
        var value = $(this).val();
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post("{{ route('admin.transfer_limit') }}", {
                key: key,
                value: value,
                _token: _token
            })
            .done(function(data) {
                toastr.success(data.message, 'Success')
            })
            .fail(function(err, textStatus, errorThrown) {
                var error_text = JSON.parse(err.responseText);
                $.each(error_text.errors, function(key, value) {
                    toastr.error(value, 'Error')
                });
            });
    });
    
    $(document).on('change', '.hour_change', function() {
        var key = $(this).attr('name');
        var value = $(this).val();
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post('', {
                key: key,
                value: value,
                _token: _token
            })
            .done(function(data) {
                toastr.success(data.message, 'Success')
            })
            .fail(function(err, textStatus, errorThrown) {
                var error_text = JSON.parse(err.responseText);
                $.each(error_text.errors, function(key, value) {
                    toastr.error(value, 'Error')
                });
            });
    });
    </script>
    @endpush