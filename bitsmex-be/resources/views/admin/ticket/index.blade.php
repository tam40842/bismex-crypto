@extends('admin.app')
@section('title', 'Ticket')
@section('content')
<div class="content_wrapper">
    <div class="page_content">
        <div class="datatable">
            <div class="table_top_actions">
                <div class="table_top_actions_right">
                    <img class="search_loading" src="" alt="Search Loading">
                    <div class="table_search">
                    <form action="{{ route('admin.ticket.search') }}" method="get">
                        <input type="text" name="search_text" class="form-control table_search_text" placeholder="Keyword...">
                        <span class="clear_search"><i class="glyphicon glyphicon-remove"></i></span>
                        <button type="submit" class="btn btn-default table_search_submit">Search</button>
                    </form>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
            @endif
            <div class="table-responsive-sm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>TICKET ID</th>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!is_null($tickets))
                        @foreach(@$tickets as $key => $value)
                        <tr>
                            <td>
                                <div class="table_title">
                                    <a href="{{ route('admin.ticket.edit', ['ticketid' => $value->ticketid]) }}" title="View this ticket">{{ $value->ticketid }}</a>
                                </div>
                            </td>
                            <td>{{ $value->username }}</td>
                            <td>
                                {{ $value->subject }}
                            </td>
                            <td>
                                {!! $status[$value->status] !!}
                            </td>
                            <td>
                                {{ $value->created_at }}
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4">Items not found.</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TICKET ID</th>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="table_bottom_actions">
				<div class="table_bottom_actions_left">
				</div>
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $tickets->count() . ' of ' . $tickets->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $tickets->appends(request()->all())->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
        </div>
    </div>
</div>
@endsection
