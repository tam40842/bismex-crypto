@extends('admin.app')
@section('title', 'Market')
@section('content')
<div class="content_wrapper">
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <div class="datatable">
            <div class="table_top_actions">
                @if(count($data) > 0)
                <div class="row">
                    <div class="col-md-6 col-12">
                        <table class="table admin_table">
                            <th class="p-3">Coins</th>
                            @foreach(@$data['CRYPTO'] as $key => $value)
                            <tr>
                                <td>{{ $value->market_name }}</td>
                                <td>
                                    <div class="switch_toggle float-right">
                                        <input type="radio"  id="market_change" name="{{ $value->market_name }}" class="switch_on" content="On" value="1"{{ $value->actived ? ' checked' : '' }}>
                                        <input type="radio"  id="market_change" name="{{ $value->market_name }}" class="switch_off" content="Off" value="0"{{ !$value->actived ? ' checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="col-md-6 col-12">
                        <table class="table admin_table">
                            <th class="p-3">FOREX</th>
                            @foreach(@$data['FOREX'] as $key => $value)
                            <tr>
                                <td>{{ $value->market_name }}</td>
                                <td>
                                    <div class="switch_toggle float-right">
                                        <input type="radio"  id="market_change" name="{{ $value->market_name }}" class="switch_on" content="On" value="1"{{ $value->actived ? ' checked' : '' }}>
                                        <input type="radio"  id="market_change" name="{{ $value->market_name }}" class="switch_off" content="Off" value="0"{{ !$value->actived ? ' checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
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
        max: 100
    });
    $(document).on('change', '#market_change', function() {
        var key = $(this).attr('name');
        var value = $(this).val();
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post('', {
                key: key,
                value: value,
                _token: _token
            })
            .done(function(data) {
                if(data.error == 1) {
                    toastr.error(data.message, 'Error')
                }else {
                    toastr.success(data.message, 'Success')
                }
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