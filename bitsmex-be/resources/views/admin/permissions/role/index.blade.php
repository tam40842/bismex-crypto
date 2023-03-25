@extends('admin.app')
@section('title')
    {{ __('Permission')}}
@endsection
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>{{ __('Permission') }}</h3>
        <a class="button_title" href="{{ route('admin.permissions.role.add') }}">{{ __('Add permissions') }}</a>
        @if($role_all)
            <a class="button_title ml-2" href="{{ route('admin.permissions.role.trash') }}">{{ __('Trash') }}</a>
        @endif
    </div>
    <div class="page_content">
        <div class="datatable">
            @include('admin.includes.boxes.notify')
            <div class="table-responsive-sm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>{{ __('Name of management rights') }}</th>
                            <th>{{ __('Time to update') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.permissions.role._item')
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="table_checkbox"><input type="checkbox" class="flat check_all_records"></th>
                            <th>{{ __('Name of management rights') }}</th>
                            <th>{{ __('Time to update') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="table_bottom_actions">
				<div class="table_bottom_actions_right">
					<div class="table_items">{!! 'Show ' . $role->count() . ' of ' . $role->total() . ' items' !!}</div>
				</div>
				<div class="table_paginate">
					{!! $role->links() !!}
				</div>
				<div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@endsection