@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>You have successfully deposited {{ $user->username }} USD to your account. Please check your account balance again.</p>
<p>If the deposited amount is incorrect, contact us immediately via email: support@bitsmex.net</p>
@stop