@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Congratulations on raising your ranks.</p>
<div style="text-align:center;">
    <img style="max-width: 150px;" src="{{ $image }}" alt="">
</div>
@stop