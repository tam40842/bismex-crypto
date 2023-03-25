@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Your AI BOT has just been activated.</p>
<p>
    <strong>Package name: {{ $package->name }}</strong><br>
    <strong>Total: {{ round($amount, 2) }} USD</strong><br>
    <strong>Package Interest: {{ $package->interest }}%/daily</strong><br>
    <strong>Package Actived at: {{ $created_at }}</strong><br>
</p>
@stop