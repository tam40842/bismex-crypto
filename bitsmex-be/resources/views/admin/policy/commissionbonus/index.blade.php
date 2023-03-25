@extends('admin.app')
@section('title', 'Bonus commission')
@section('content')
<div class="content_wrapper">
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <div class="datatable">
            <div class="table_top_actions">
                <div class="col-md-6 col-12 p-2">
                    <div class="form-group">
                        <label for="from"><strong>Bonus commission</strong></label>
                        <input type="text" class="js-range-slider" id="hour_change" name="bonus_commission_percent"
                            value="{{ $commissionbonus->setting_value }}" />
                    </div>
                    <div class="clearfix"></div>
                </div>
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
    $(document).on('change', '#hour_change', function() {
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