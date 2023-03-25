@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username ?? '' }}</strong></p>
<p>Bistrading security system has found your account suspicious of fraud. This could be a mistake or your account is being hacked. Please contact the Bistrading team for the best possible support.
</p>
@stop