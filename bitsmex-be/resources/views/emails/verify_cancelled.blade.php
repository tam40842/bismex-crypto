@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Your information for account verification at {{ request()->getHost() }} has been declined.</p>
<p>
    Reason: <b>{{ $reason }}</b><br>
    Please sign in and go to the "ID Verification" section in the "Profile" category to check again and replace it with more accurate photos
</p>
<p>
    If you need any support for this process, please contact us via 
<a href="https://{{ request()->getHost() }}" target="_blank">https://{{ request()->getHost() }}</a>
</p>

@stop