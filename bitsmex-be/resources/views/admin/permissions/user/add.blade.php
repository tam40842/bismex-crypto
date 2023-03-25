@extends('admin.app')
@section('title')
 {{ __('Add user')}}
@endsection
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>{{ __('Add user') }}</h3>
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
                                        <input type="text" name="username" value="" class="form-control w-100" required="required" />
                                        <small class="text-dark">{{ $errors->first('username') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Choose the right') }}</th>
                                    <td>
                                        @foreach(@$role as $value)
                                        <div class="radio pb-1">
                                            <label>
                                                <input type="radio" value="{{ $value->slug }}" name="permission"> {{ $value->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="submit" class="btn btn-primary deposit_btn">{{ __('Add') }}</button>
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
