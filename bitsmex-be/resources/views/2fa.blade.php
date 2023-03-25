<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') {!! $options['seo_separator'] . ' ' . $options['title_website'] !!} Admin</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="{{ $options['site_logo'] }}"/>
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="{{ asset('login/images/icons/favicon.ico') }}"/>
<!--===============================================================================================-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('login/vendor/animate/animate.css') }}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{ asset('login/css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('login/css/main.css') }}">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url({{ asset('login/images/board_bg.png') }});">
			<div class="wrap-login100 p-t-30 p-b-50">
				<span class="login100-form-title p-b-41">
					2FA Login
				</span>
                @include('admin.includes.boxes.notify')
				<form action="{{ route('post_login_2fa') }}" method="POST" class="login100-form validate-form p-b-33 p-t-5">
                    @csrf
					<!-- <div class="wrap-input100 validate-input" data-validate = "Enter 2FA">
						<input class="input100" type="text" name="twofa_code" placeholder="2FA">
						<span class="focus-input100" data-placeholder="&#xe82a;"></span>
					</div> -->

					<div class="wrap-input100 validate-input" data-validate="Enter 2FA">
						<input class="input100" type="text" name="twofa_code" placeholder="Code 2FA">
						<span class="focus-input100" data-placeholder="&#xe80f;"></span>
					</div>

					<div class="container-login100-form-btn m-t-32">
						<button type="submit" class="login100-form-btn">
							Submit
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
	<script src="{{ asset('login/vendor/animsition/js/animsition.min') }}"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>