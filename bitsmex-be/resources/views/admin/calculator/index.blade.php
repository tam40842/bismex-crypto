@extends('admin.app')
@section('title', 'Calculator commission')
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>Calculator commission</h3>
    </div>
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <div class="datatable">
			<div class="table_top_actions">
				<div class="table_top_actions_left">
					<form action="{{ route('admin.calculator.search') }}" method="post">
                        @csrf
						<div class="d-flex justify-content-between">
							<div class="form-group">
								<label for="from"><strong>Date from</strong></label>
								<input type="date" class="form-control" name="date_from" value="{{ @$filter['date_from'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Date to</strong></label>
								<input type="date" class="form-control" name="date_to" value="{{ @$filter['date_to'] }}" />
							</div>
							<div class="form-group">
								<label for="from"><strong>Username or email</strong></label>
								<input type="text" class="form-control" name="username" required="required" value="{{ isset($filter['username']) ? $filter['username'] : '' }}" placeholder="Username or email"/>
							</div>
							<button type="submit" class="btn btn-primary mt-4">Filter</button>
						</div>
					</form>
				</div>
                <div class="table_top_actions_right">
					<div class="table_search">
						<input type="text" name="ratio" id="shares" class="form-control table_search_text">
						<span class="clear_search"><i class="fa fa-percent fa-fw mt-3"></i></span>
						<div class="btn btn-primary" style="padding-top: 12px">Bonus commission</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
            <div class="row">
                @foreach($stastics_total as $value)
                <div class="col-md-6 col-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <div class="fluid-container">
                                        <i class="menu-icon fa fa-usd fa-fw fa-3x text-warning"></i>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <div class="fluid-container">
                                        <p class="mb-0 text-right">{{ $value['name'] }}</p>
                                        <h3 class="font-weight-medium text-right mb-0">
                                            ${{ number_format($value['total'], 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-md-6 col-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <div class="fluid-container">
                                        <i class="menu-icon fa fa-usd fa-fw fa-3x text-warning"></i>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <div class="fluid-container">
                                        <p class="mb-0 text-right">Tiền hoa hồng cần chi trả cho user</p>
                                        <h3 class="font-weight-medium text-right mb-0" id="result">
                                            $0.00
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <button class="btn btn-primary mt-4" id="save-data">Bonus user</button>
            </div>
		</div>
    </div>
    @if(!is_null($user_bonus))
    <div class="page_content pt-4">
		<div class="x_panel">
			<div class="x_title">
				<h2>Histories bonus {{ @$user_bonus->username }}</h2>
			</div>
			<table class="table table-bordered">
				<thead>
					<th>Admin bonus</th>
					<th>Ratio</th>
					<th>Total</th>
					<th>Created at</th>
				</thead>
				<tbody>
					@if(count($histories_bonus) > 0)
					@foreach($histories_bonus as $key => $value)
					<tr>
						<td>{{ $value->admin_bonus }}</td>
						<td>{{ $value->ratio }}%</td>
						<td>${{ number_format($value->total, 2) }}</td>
						<td>{{ $value->created_at }}</td>
					</tr>
					@endforeach
					@else
					<tr>
						<td colspan="4" class="text-center">No results.</td>
					</tr>
					@endif
				</tbody>
			</table>
			{!! $histories_bonus->links() !!}
		</div>
	</div>
    @endif
</div>
@endsection
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $("#save-data").click(function(event){
            event.preventDefault();

            let username = $("input[name=username]").val();
            let ratio = $("input[name=ratio]").val();
            let total_profit = "{{ $stastics_total['total_profit']['total'] }}"
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: "{{ route('admin.calculator.BonusUser') }}",
                type:"POST",
                data:{
                username: username,
                total_profit: total_profit,
                ratio: ratio,
                _token: _token
                },
                success:function(response){
                    console.log(response)
                    if(response.status != 200) {
                        toastr.error(response.message, 'Error')
                        return false;
                    }
                    toastr.success(response.message, 'Success')
                },
                error: function (data) {
                    var response = JSON.parse(data.responseText);
                    $.each( response.errors, function( key, value) {
                        toastr.error(value, 'Error')
                    });
                }
            });
        });

        $(document).ready(function(){
            $('#shares').keyup(function(){
                var total_profit = parseInt("{{ $stastics_total['total_profit']['total'] }}")
                var bonus_user = $('#shares').val() * total_profit / 100
                $('#result').text('$'+bonus_user.toFixed(2));
            });   
        });
    </script>
@endpush