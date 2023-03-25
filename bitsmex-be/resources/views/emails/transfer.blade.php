@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>We have just recorded a transfer of money from your account.</p>
<p>
    <strong>Transfer amount: {{ round($amount, 2) }} USD</strong><br>
    <strong>Reciever: {{ $receiver }}</strong><br>
    <strong>Created at: {{ $created_at }}</strong><br>
</p>
<p>
    If this is not your activity, please contact us immediately via email: support@bitsmex.net
</p>
@stop