@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Congratulations, you have just received a system development commission from Bistrading.</p>
<p>
    <strong>Week: {{ $week }}</strong><br>
    <strong>Commission amount: {{ number_format($amount, 2) }} USD</strong><br>
    <strong>Branch volume: {{ number_format($volume, 2) }} USD</strong><br>
</p>
@stop