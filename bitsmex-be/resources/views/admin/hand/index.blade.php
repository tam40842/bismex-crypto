@extends('admin.app')
@section('title', 'Chỉnh tay')
@section('content')
<div class="content_wrapper">
    @include('admin.includes.boxes.notify')
    <div class="page_content py-5">
        <div class="row">
            <div class="col-12">
                <div id="list_markets" class="d-inline-block">
                    <label class="btn btn-dark" onclick="select_market('ALL')">
                        <input type="radio" name="market_name" id="ALL" value="ALL"> ALL
                    </label>
                    @foreach($markets as $key => $value)
                    <label class="btn btn-dark" onclick="select_market('{{ $value->market_name }}')">
                        <input type="radio" name="market_name" id="{{ $value->market_name }}" value="{{ $value->market_name }}"> {{ $value->market_name }}
                    </label>
                    @endforeach 
                </div>
                <div class="progress">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" id="sell">SELL 50%</div>
                    <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" id="buy">BUY 50%</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="text-danger">
                        <h3><strong>SELL:</strong> $<span id="sell_total">0.00</span></h3>
                    </div>
                    <div class="text-warning">
                        <h3><strong>TIME:</strong> <span id="timer">0</span></h3>
                    </div>
                    <div class="text-success">
                        <h3><strong>BUY:</strong> $<span id="buy_total">0.00</span></h3>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-danger" value="SELL" id="adjust">
                        <i class="fa fa-hand-paper-o"></i>
                        <span class="mt-1">SELL</span>
                    </button>
                    <button class="btn btn-success" value="BUY" id="adjust">
                        <i class="fa fa-hand-paper-o"></i>
                        <span class="mt-1">BUY</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-12 col-12">
                <div class="card">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="current_round-tab" data-toggle="tab" href="#current_round" role="tab" aria-controls="current_round" aria-selected="true">Current Round</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="last_round-tab" data-toggle="tab" href="#last_round" role="tab" aria-controls="last_round" aria-selected="false">Last Round</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="current_round" role="tabpanel" aria-labelledby="current_round-tab">
                            <div class="card-body">
                                <table class="table table-tripped" id="list_orders">
                                    <thead>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Market</th>
                                        <th>
                                            <a href="javascript:;" style="text-decoration: none" data-name="sort" data-value="amount" data-sort="asc">Amount <i class="fa fa-long-arrow-down"></i></a>
                                        </th>
                                        <th>
                                            <a href="javascript:;" style="text-decoration: none" data-name="sort" data-value="action" data-sort="asc">Option <i class="fa fa-long-arrow-down"></i></a>
                                        </th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="last_round" role="tabpanel" aria-labelledby="last_round-tab">
                            <div class="card-body">
                                <table class="table table-tripped" id="last_round">
                                    <thead>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Market</th>
                                        <th>Amount</th>
                                        <th>Option</th>
                                        <th>Result</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('css')
<style>
.progress .progress-bar {
    border-radius: unset;
}
.progress {
    height: 25px;
}
#list_markets label {
    padding: 10px;
}
</style>
@endpush
@push('js')
<script>
    var sort_name = '';
    var sort_value = '';
    var market_name = 'ALL';

    $(document).on("click", "a[data-name=sort]", function() {
        var o = $(this);
        if(o.attr('data-sort') == 'asc') {
            o.attr('data-sort', 'desc').html(o.text()+' <i class="fa fa-long-arrow-down"></i>');
        } else {
            o.attr('data-sort', 'asc').html(o.text()+' <i class="fa fa-long-arrow-up"></i>');
        }
        sort_name = o.attr('data-value');
        sort_value = o.attr('data-sort');
        getListOrder();
    })
    if($('[name=market_name]').is(':checked')) { 
        alert("it's checked");
    }
    $(document).ready(function() {
        setInterval(() => {
            let current_time = new Date();
            if(current_time.getSeconds() <= 30) {
                getListOrder();
                getLastRound();
            }
            if(current_time.getSeconds() < 5) {
                $('[name=market_name]').each(function() {
                    var o = $(this);
                    o.closest('label').attr('class', 'btn btn-dark');
                })
            }
            $('#timer').text(current_time.getSeconds()+'s');
        }, 1000);
    });

    $(document).on('click', '#adjust', function() {
        var market_name = $('input[name="market_name"]:checked').val();
        var round_value = $(this).val();
        var _token = $('meta[name="csrf-token"]').attr('content');
        if(market_name == '' || market_name == undefined) {
            alert('Vui lòng chọn 1 thị trường muốn điều chỉnh.');
            return false;
        }
        // alertify.confirm('System Notice', "Bạn có chắc chắn chọn " + round_value + " ?", function () {
            $.post("{{ route('admin.hand') }}", {_token : _token, market_name : market_name, round_value : round_value}, function(data) {
                if(data.error) {
                    alertify.error(data.message);
                    return false;
                }
                alertify.success(data.message);
            }, 'json');
        // }, function () {
        //     // alertify.error('Cancel')
        // });
    });

    function getListOrder() {
        $.get("{{ route('admin.hand.orders') }}?market_name="+market_name+"&sort_name="+sort_name+"&sort_value="+sort_value, {}, function(data) {
            if(data.error) {
                return false;
            }
            if (data.data.sell != 0 || data.data.buy != 0) {
                var sell = Math.round(data.data.sell / (data.data.sell + data.data.buy) * 100);
                var buy = Math.round(data.data.buy / (data.data.sell + data.data.buy) * 100);
            } else {
                var sell = 50;
                var buy = 50;
            }
            $('#buy').attr('aria-valuenow', buy).css({'width':buy+'%'}).html('BUY '+buy+'%');
            $('#sell').attr('aria-valuenow', sell).css({'width':sell+'%'}).html('SELL '+sell+'%');
            $('#sell_total').text(data.data.sell.toFixed(2));
            $('#buy_total').text(data.data.buy.toFixed(2));
            var html = '';
            $.each(data.data.orders, function(key, value) {
                var color = value.action == 'BUY' ? 'success' : 'danger';
                html += `<tr>`;
                html += `<td>${key + 1}</td>`;
                html += `<td>${value.username}</td>`;
                html += `<td>${value.market_name}</td>`;
                html += `<td>$${value.amount.toFixed(2)}</td>`;
                html += `<td><span class="badge badge-${color}">${value.action}</span></td>`;
                html += `</tr>`;
                $('#'+value.market_name).closest('label').attr('class', 'btn btn-warning');
            });
            $("#list_orders").find('tbody').html(html);
        }, 'json');
    }

    function getLastRound() {
        $.get("{{ route('admin.hand.last_orders') }}", {}, function(data) {
            if(data.error) {

                return false;
            }
            var html = '';
            $.each(data.data.orders, function(key, value) {
                var color = value.action == 'BUY' ? 'success' : 'danger';
                var result = value.status == 1 ? 'WIN' : 'LOSE';
                var result_color = value.status == 1 ? 'success' : 'danger';
                html += `<tr>`;
                html += `<td>${key + 1}</td>`;
                html += `<td>${value.username}</td>`;
                html += `<td>${value.market_name}</td>`;
                html += `<td>$${value.amount.toFixed(2)}</td>`;
                html += `<td><span class="badge badge-${color}">${value.action}</span></td>`;
                html += `<td><span class="badge badge-${result_color}">${result}</span></td>`;
                html += `</tr>`;
            });
            $("#last_round").find('tbody').html(html);
        }, 'json');
    }

    function select_market(val) {
        market_name = val;
        getListOrder();
    }
</script>
@endpush