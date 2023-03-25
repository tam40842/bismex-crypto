@extends('admin.app')
@section('title')
    {{ __('Permission') }}
@endsection
@section('content')
    <div class="content_wrapper">
        <div class="page_title">
            <h3>{{ __('Trash Permission') }}</h3>
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
                            @if (count($role) > 0)
                                @foreach ($role as $value)
                                    <tr>
                                        <td><input type="checkbox" value="{!!  $value->id !!}" class="flat check_item"
                                                name="table_records"></td>
                                        <td>
                                            <div class="table_title text-uppercase">
                                                <small>
                                                    <a href="{{ route('admin.permissions.role.edit', ['id' => $value->id]) }}"
                                                        title="Edit order">{{ $value->name }}</a>
                                                </small>
                                            </div>
                                            <ul class="table_title_actions">
                                                <li>
                                                    <a
                                                        href="{{ route('admin.permissions.role.restore', ['id' => $value->id]) }}"
                                                        onclick="return confirm('{{ __('Are you sure to restore the permission ?') }}')">{{ __('Restore') }}</a>
                                                </li>
                                                <li>
                                                    <a class="text-danger"
                                                        href="{{ route('admin.permissions.role.deleteTrash', ['id' => $value->id]) }}"
                                                        onclick="return confirm('{{ __('Are you sure to delete the permission ?') }}')">{{ __('Delete') }}</a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>{{ $value->created_at }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('Items not found.') }}</td>
                                </tr>
                            @endif
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
