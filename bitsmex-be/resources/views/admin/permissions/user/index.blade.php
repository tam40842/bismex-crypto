@extends('admin.app')
@section('title')
  {{ __('Administered')}}
@endsection
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>{{ __('Administered') }}</h3>
        <a class="button_title" href="{{ route('admin.permissions.user.add') }}">{{ __('Add user') }}</a>
    </div>
    <div class="page_content">
        <div class="datatable">
            @include('admin.includes.boxes.notify')
            <div class="table-responsive-sm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Permission') }}</th>
                            <th>{{ __('Created at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.permissions.user._item')
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Permission') }}</th>
                            <th>{{ __('Created at') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="table_bottom_actions">
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $user->count() . ' of ' . $user->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $user->links() !!}
				</div>
				<div class="clearfix"></div>
			</div>
        </div>
    </div>
</div>
@endsection
