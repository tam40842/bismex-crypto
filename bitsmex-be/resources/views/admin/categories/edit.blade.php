@extends('admin.app')
@section('title', 'Categories')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Add categories</h3>
	</div>
	@include('admin.includes.boxes.notify')
    
	<div class="page_content">
    @if(session('success'))
    <div class="alert alert-success">
        {{session('success')}}
    </div>
    @endif
		<form action="" method="POST" class="deposit-form">
			@csrf
			<div class="x_panel">
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <table class="table admin_table">
                                <tr>
                                    <th>Category name</th>
                                    <td>
                                        <input type="text" name="name" value="{{ $categories->name }}" class="form-control w-100" placeholder="The Category name" required>
                                        <small class="text-dark">{{ $errors->first('name') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="submit" class="btn btn-primary deposit_btn">Update</button>
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