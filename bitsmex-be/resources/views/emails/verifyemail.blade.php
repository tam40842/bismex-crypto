@extends('emails.layout')
@section('content')
<p><strong>Dear {{ $user->username }}</strong></p>
<p>Thanks for registering an account on Bitsmex. Please take a second to activate registration to ensure that we've gotten the right email address. Simply click the button below:
    Or you can access this following link:</p>
    <p>If you have any questions, please contact us at {{config('app.support_email', 'support@bitsmex.net') }}</p> 
    <p>GENERAL RISK WARNING:</p>
    <p>The financial products offered by the company carry a high level of risk and can result in the loss of all your funds. You should never invest money that you cannot afford to lose.</p>
    <p>Terms & Conditions</p>
<a href="{{ env('APP_BE').'/verify?token='.$code }}" style="padding:10px 28px;background:none;text-decoration:none;border:2px solid #262a42;color:#262a42;text-transform:uppercase;font-size:14px" target="_blank">Confirm</a>
</p>
<p>
If you can't open link on the button. Please connect to below link 
<a href="{{ env('APP_BE').'/verify?token='.$code }}" target="_blank">{{ env('APP_BE').'/verify?token='.$code }}</a>
</p>
@stop