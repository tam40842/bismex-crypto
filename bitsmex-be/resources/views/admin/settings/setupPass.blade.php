@extends('admin.app')
@section('title', 'Thiết lập mật khẩu thành viên')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Thiết lập mật khẩu thành viên</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="post">
			@csrf
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<table class="admin_table">
								<tr>
									<th>Thành viên thiết lập cuối cùng</th>
									<td>
										<input type="text" disabled name="email" class="form-control" value="{{ $userSetPass->email }}" required>
									</td>
								</tr>
								<tr>
									<th>Mật khẩu mới</th>
									<td>
										<input type="password" name="password" class="form-control" value="" required>
									</td>
								</tr>
                                <tr>
									<th>Nhập lại mật khẩu</th>
									<td>
										<input type="password" name="password_confirmation" class="form-control" value="" required>
									</td>
								</tr>
							</table>
                            <div class="text-left">
                                <button class="btn btn-primary" type="submit">Lưu cài đặt</button>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

    <div class="page_title mt-3">
		<h3>Lịch sử thành viên thiết lập</h3>
    </div>
    <table class="table table-bordered">
        <thead class="text-center">
            <th>Email</th>
            <th>Ngày thiết lập</th>
        </thead>
        <tbody>
            @if(count($listSetPass))
            @foreach($listSetPass as $key => $value)
            <tr>
                <td>{{ $value->email }}</td>
                <td>{{ $value->created_at }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="2" class="text-center">No results.</td>
            </tr>
            @endif
        </tbody>
    </table>
    {{ $listSetPass->links() }}
</div>
@stop
@push('css')
<style>
	.generate_password_input{
		display: none;
		width: 25em;
		position: relative;
	}
	.generate_password_input .field_password{
		padding-right: 40px;
	}
	.generate_password_input .show_hide_pass{
		position: absolute;
		top: 0;
		right: -25px;
		width: 35px;
		height: 28px;
		text-align: center;
		cursor: pointer;
		padding-top: 3px;
	}
	.remove_featured_image button{
		margin-top: 35px;
	}
</style>
@endpush