<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="base_url" content="{{ url('/') }}">
    <link rel="shortcut icon" href="{{ @$options['favicon'] }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maintenance {{ $options['title_website'] }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('maintainance/css/ionicons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('maintainance/css/jquery.classycountdown.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('maintainance/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('maintainance/css/maintenance.css') }}">
    @stack('css')
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="{{ asset('maintainance/js/jquery.min.js') }}"></script>
</head>
<body>
	<div class="main-area">
        <div class="text-center full-height position-static">
            <section class="full-height">
                <div class="display-table">
                    <div class="display-table-cell">
                        <div class="main-content">
                            <a class="logo" href="#"><img src="{{ $options['site_logo'] }}" alt="Logo" style="max-width: 200px;"></a>
                            <h1 class="title text-center">WEBSITE MAINTENANCE</h1>
                            {!! $options['maintenance_content'] !!}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('maintainance/js/tether.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('maintainance/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('maintainance/js/jquery.classycountdown.js') }}"></script>
    <script type="text/javascript" src="{{ asset('maintainance/js/jquery.knob.js') }}"></script>
    <script type="text/javascript" src="{{ asset('maintainance/js/jquery.throttle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('maintainance/js/scripts.js') }}"></script>
    @if(isset($options['tawk_to_id']))
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/{{ $options['tawk_to_id'] }}/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
    @endif
</body>
</html>
