<?php
namespace App\Http\Controllers\Vuta;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\User;

class VutaMail {

	public static function send($to, $subject, $blade, Array $data = []){
		$mail = new PHPMailer(true);
		try {
			//Server settings
			$mail->SMTPDebug = 0;
			$mail->CharSet = 'UTF-8';
			$mail->isSMTP(); 
			$mail->Host = env('MAIL_HOST');
			$mail->SMTPAuth = true;
			$mail->Username = env('MAIL_USERNAME');
			$mail->Password = env('MAIL_PASSWORD');
			$mail->SMTPSecure = env('MAIL_ENCRYPTION');
			$mail->Port = env('MAIL_PORT');

			//Recipients
			$mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
			$mail->addAddress($to);

			//Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = (string)view('emails.' . $blade, $data)->render();
			// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return 'Message has been sent';
		} catch (Exception $e) {
			return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
		}
	}

}