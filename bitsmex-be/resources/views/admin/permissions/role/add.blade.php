@extends('admin.app')
@section('title')
    {{ __('Add permissions')}}
@endsection
@section('content')
<div class="content_wrapper">
    <div class="page_title">
        <h3>{{ __('Add permissions') }}</h3>
    </div>
    @include('admin.includes.boxes.notify')
    <div class="page_content">
        <form action="" method="POST" class="">
            @csrf
            <div class="x_panel">
                <div class="x_content">
                    <table class="table admin_table">
                        <tr>
                            <th>{{ __('Right name') }}</th>
                            <td>
                                <input type="text" name="name" class="form-control w-100"
                                    value="{{ old('name') }}" placeholder="{{ __('Right name') }}" required>
                                <small class="text-dark">{{ $errors->first('name') }}</small>
                            </td>
                        </tr>
                    </table>
                    <table class="table table-tripped">
                        <thead>
                            <th>{{ __('Module name') }}</th>
                            <th>{{ __('View') }}</th>
                            <th>{{ __('Add') }}</th>
                            <th>{{ __('Edit') }}</th>
                            <th>{{ __('Delete') }}</th>
                        </thead>
                        <tbody>
                            @foreach($modules as $value)
                            <tr>
                                <td><i>{{ $value }}</i></td>
                                <td>
                                    <input type="checkbox" name="permissions[]" value="{{ $value . '_access' }}">
                                </td>
                                <td>
                                    <input type="checkbox" name="permissions[]" value="{{ $value . '_add' }}">
                                </td>
                                <td>
                                    <input type="checkbox" name="permissions[]" value="{{ $value . '_edit' }}">
                                </td>
                                <td>
                                    <input type="checkbox" name="permissions[]" value="{{ $value . '_delete' }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-primary">{{ __('Save Permission') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection