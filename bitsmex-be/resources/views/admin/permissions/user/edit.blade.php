@extends('admin.app')
@section('title')
 {{ __('Edit user role')}}
@endsection
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>{{ __('Edit user role') }}</h3>
	</div>
    @include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST" class="deposit-form">
			@csrf
			<div class="x_panel">
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <table class="table admin_table">
                                <tr>
                                    <th>{{ __('Username') }}</th>
                                    <td>
                                        <input type="text" name="name" value="{{ $user->username }}" class="form-control w-100" placeholder="{{ __('Username') }}" required>
                                        <small class="text-dark">{{ $errors->first('name') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Choose the right') }}</th>
                                    <td>
                                        @foreach(@$roles as $value)
                                        <div class="radio pb-1">
                                            <label><input type="radio" value="{{ $value->slug }}" {{ ($value->slug == $user->permission) ? 'checked' : '' }} name="permission">{{ $value->name }}</label>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="submit" class="btn btn-primary deposit_btn">{{ __('Edit') }}</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		</form>
	</div>
</div>
@endsection
