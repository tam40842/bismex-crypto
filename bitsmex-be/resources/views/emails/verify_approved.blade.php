@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Documentation for account verification at {{ request()->getHost() }} has been accepted.</p>
<p>
If you need more information, you can contact us via the online support function at
<a href="https://{{ request()->getHost() }}" target="_blank">https://{{ request()->getHost() }}</a>
</p>
@stop