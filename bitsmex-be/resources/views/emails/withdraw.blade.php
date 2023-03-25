@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>We are processing your withdraw request, your withdraw request will be completed in 5 - 60 minutes.
</p>
<p>
    <strong>withdraw amount: {{ round($withdraw->amount, 2) }} USD</strong><br>
    <strong>withdraw fee: {{ round($withdraw->fee, 2) }} USD</strong><br>
    <strong>To Address: {{ $withdraw->output_address }}</strong><br>
</p>
@stop