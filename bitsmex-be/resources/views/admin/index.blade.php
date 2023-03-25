@extends('admin.app')
@section('title', __('Dashboard'))
@section('content')
    <div class="content_wrapper">
        @include('admin.includes.boxes.notify')
        <div class="page_content">
            <div class="row pb-4">
                <div class="col-12">
                    <div class="tab">
                        <button class="tablinks" onclick="openCity(event, 'all')" id="defaultOpen">Thống kê toàn bộ</button>
                        <button class="tablinks" onclick="openCity(event, 'day')">Thống kê trong ngày</button>
                    </div>
                    <div id="all" class="tabcontent">
                        <div class="row">
                            @foreach ($stastics_total as $key => $value)
                                <div class="col-md-3 col-6 pb-2 stretch-card">
                                    <div class="card card-statistics">
                                        <div class="card-body">
                                            <div class="clearfix">
                                                <div class="float-left">
                                                    <div class="fluid-container">
                                                        <i
                                                            class="menu-icon {{ $value['icon'] }} fa-fw fa-3x text-muted"></i>
                                                    </div>
                                                </div>
                                                <div class="float-right">
                                                    <div class="fluid-container">
                                                        <p class="mb-0 text-right">{{ $value['name'] }}</p>
                                                        <h3 class="font-weight-medium text-right mb-0">
                                                            {{ $value['type'] == 'money' ? number_format($value['total'], 2) : number_format($value['total']) }}
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="day" class="tabcontent">
                        <div class="row">
                            @foreach ($stastics_day as $key => $value)
                                <div class="col-md-3 col-6 pb-2 stretch-card">
                                    <div class="card card-statistics">
                                        <div class="card-body">
                                            <div class="clearfix">
                                                <div class="float-left">
                                                    <div class="fluid-container">
                                                        <i
                                                            class="menu-icon {{ $value['icon'] }} fa-fw fa-3x text-muted"></i>
                                                    </div>
                                                </div>
                                                <div class="float-right">
                                                    <div class="fluid-container">
                                                        <p class="mb-0 text-right">{{ $value['name'] }}</p>
                                                        <h3 class="font-weight-medium text-right mb-0">
                                                            {{ $value['type'] == 'money' ? number_format($value['total'], 2) : number_format($value['total']) }}
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-6">
                    <div class="datatable">
                        <div class="table-responsive-sm">
                            <h3>Users thắng/24h</h3>
                            <table class="table table-bordered" id="sort_win">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Total Profit</th>
                                        <th>No.WIN</th>
                                        <th>No.LOSE</th>
                                        <th>Live Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($user_trade24h) > 0)
                                        @foreach ($user_trade24h as $value)
                                            @if ($value->user_type == 'win')
                                                <tr>
                                                    <td>
                                                        <div class="table_title">
                                                            {{ $value->username }}
                                                        </div>
                                                    </td>
                                                    <td class="text-success">+${{ number_format($value->win_total, 2) }}
                                                    </td>
                                                    <td>{{ $value->win_count }}</td>
                                                    <td>{{ $value->lose_count }}</td>
                                                    <td>${{ number_format($value->live_balance, 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" class="text-center">Không có lệnh nào.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="table_bottom_actions">
                            <div class="table_bottom_actions_left">

                            </div>
                            <div class="table_bottom_actions_right">
                                {{-- <div class="table_items">{!! 'Show ' . $user_trade24h->count() . ' of ' . $user_trade24h->total() . ' items' !!}</div> --}}
                            </div>
                            <div class="table_paginate">
                                {{-- {!! $user_trade24h->links() !!} --}}
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="datatable">
                        <div class="table-responsive-sm">
                            <h3>Users thua/24h</h3>
                            <table class="table table-bordered" id="sort_lose">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Total Profit</th>
                                        <th>No.WIN</th>
                                        <th>No.LOSE</th>
                                        <th>Live Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($user_trade24h) > 0)
                                        @foreach ($user_trade24h as $value)
                                            @if ($value->user_type == 'lose')
                                                <tr>
                                                    <td>
                                                        <div class="table_title">
                                                            {{ $value->username }}
                                                        </div>
                                                    </td>
                                                    <td class="text-danger">-${{ number_format($value->lose_total, 2) }}
                                                    </td>
                                                    <td>{{ $value->win_count }}</td>
                                                    <td>{{ $value->lose_count }}</td>
                                                    <td>${{ number_format($value->live_balance, 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" class="text-center">Không có lệnh nào.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="table_bottom_actions">
                            <div class="table_bottom_actions_left">

                            </div>
                            <div class="table_bottom_actions_right">
                                {{-- <div class="table_items">{!! 'Show ' . $user_trade24h->count() . ' of ' . $user_trade24h->total() . ' items' !!}</div> --}}
                            </div>
                            <div class="table_paginate">
                                {{-- {!! $user_trade24h->links() !!} --}}
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Lợi nhuận giao dịch trong 30 ngày</h3>
                </div>
                <div class="card-body">
                    <canvas id="systemProfit" width="400" height="100"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Số lượng Volume trong 30 ngày</h3>
                </div>
                <div class="card-body">
                    <canvas id="total_volume_date" width="400" height="100"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Tài Khoản Đăng Kí Trong 30 Ngày</h3>
                </div>
                <div class="card-body">
                    <canvas id="register_stastics" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop
@push('css')
    <style>
        .tab {
            overflow: hidden;
            border-bottom: 1px solid #ccc;
            background-color: #f1f1f1;
        }

        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 0.75rem 1.25rem;
            transition: 0.3s;
            font-size: 17px;
        }

        .tab button:hover {
            background-color: #ddd;
        }

        .tab button.active {
            background-color: #ccc;
        }

        .tabcontent {
            display: none;
            padding: 8px 12px 0px;
            border: 1px solid #ccc;
            border-top: none;
        }

    </style>
@endpush
@push('js')
    <script>
        $(document).ready(function() {
            $("#sort_win").DataTable({
                columnDefs: [{
                    type: 'date',
                    targets: [3]
                }],
            });
        });
        $(document).ready(function() {
            $("#sort_lose").DataTable({
                columnDefs: [{
                    type: 'date',
                    targets: [3]
                }],
            });
        });

        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito',
            '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';
        var systemProfit = document.getElementById('systemProfit');
        var systemProfit = document.getElementById('systemProfit').getContext('2d');
        var systemProfit = $('#systemProfit');
        var systemProfit = 'systemProfit';
        var systemProfit = document.getElementById('systemProfit');
        var systemProfit_BarChart = new Chart(systemProfit, {
            type: 'bar',
            data: {
                labels: ["{!! implode('","', $order_label) !!}"],
                datasets: [{
                    label: 'Profit',
                    data: [{{ implode(',', $order_data) }}],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var register_stastics = document.getElementById('register_stastics');
        var register_stastics = document.getElementById('register_stastics').getContext('2d');
        var register_stastics = $('#register_stastics');
        var register_stastics = 'register_stastics';
        var register_stastics = document.getElementById('register_stastics');
        var register_stastics_BarChart = new Chart(register_stastics, {
            type: 'bar',
            data: {
                labels: ["{!! implode('","', $user_label) !!}"],
                datasets: [{
                    label: 'User',
                    data: [{{ implode(',', $user_data) }}],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var total_volume_date = document.getElementById('total_volume_date');
        var total_volume_date = document.getElementById('total_volume_date').getContext('2d');
        var total_volume_date = $('#total_volume_date');
        var total_volume_date = 'total_volume_date';
        var total_volume_date = document.getElementById('total_volume_date');
        var total_volume_date_BarChart = new Chart(total_volume_date, {
            type: 'bar',
            data: {
                labels: @json($orders_volume_date),
                datasets: [{
                    label: 'Volume date',
                    data: {{ $orders_volume }},
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        document.getElementById("defaultOpen").click();

    </script>
@endpush
