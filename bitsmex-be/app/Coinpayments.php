<?php
namespace App;

class Coinpayments 
{
	public function call($cmd, $req = array()) { 
		$public_key = config('app.COINPAYMENTS_API_KEY'); 
		$private_key = config('app.COINPAYMENTS_API_SECRET'); 
		$req['version'] = 1; 
		$req['cmd'] = $cmd; 
		$req['key'] = $public_key; 
		$req['format'] = 'json';
		 
		$post_data = http_build_query($req, '', '&');
		$hmac = hash_hmac('sha512', $post_data, $private_key); 
		
		static $ch = NULL; 
		if ($ch === NULL) { 
			$ch = curl_init('https://www.coinpayments.net/api.php'); 
			curl_setopt($ch, CURLOPT_FAILONERROR, TRUE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		} 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('HMAC: '.$hmac)); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
		$data = curl_exec($ch);                 
		if ($data !== FALSE) { 
			if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) { 
				$dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING); 
			} else { 
				$dec = json_decode($data, TRUE); 
			} 
			if ($dec !== NULL && count($dec)) { 
				return $dec; 
			} else { 
				return array('error' => 'Unable to parse JSON result ('.json_last_error().')'); 
			} 
		} else { 
			return array('error' => 'cURL error: '.curl_error($ch)); 
		} 
	} 
}