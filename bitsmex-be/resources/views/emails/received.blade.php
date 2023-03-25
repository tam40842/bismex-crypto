@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>You have just received a money transfer from {{ $sender }}.</p>
<p>
    <strong>Received amount: {{ round($amount, 2) }} USD</strong><br>
    <strong>Received at: {{ $created_at }}</strong><br>
</p>
@stop