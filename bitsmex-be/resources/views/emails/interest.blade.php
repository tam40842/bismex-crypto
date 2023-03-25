@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>You have just received a profit from AI BOT.</p>
<p>
    <strong>AI BOT: {{ $robot_id }}</strong><br>
    <strong>Profit amount: {{ round($amount, 2) }} USD</strong><br>
    <strong>Created at: {{ $created_at }}</strong><br>
</p>
@stop