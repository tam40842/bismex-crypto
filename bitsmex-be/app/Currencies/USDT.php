<?php
namespace App\Currencies;

use Illuminate\Support\Facades\Cache;

class USDT {
    public $symbol;

    public function __construct() {
        $this->symbol = 'USDT';
    }

    public function generateAddress($index) {
		$get = json_decode(file_get_contents('https://bep20.bitsmex.net/api/v1/account/generate/'.$index), true);
        $status = isset($get['address']) ? array('status' => true, 'symbol' => $this->symbol, 'address' => $get['address']) : array('status' => false);
        return json_encode($status);
    }

	public function validator($address) {
        $validate = preg_match('/^0x[0-9A-Fa-f]{40}/i', $address);
        return $validate;
	}
    
    public function rate() {
        return 1;
    }
    
    public function percent_change() {
        return 0;
    }
    
    public function transactionLink($address) {
        $url = '<a href="https://bscscan.com/address/'.$address.'" target="_blank">'.$address.'</a>';
        return $url;
    }
    
    public function hashLink($tx_id) {
		$url = 'https://bscscan.com/tx/'.$tx_id;
		return $url;
    }
    
    public function addressLink($address) {
		$url = 'https://bscscan.com/address/'.$address;
		return $url;
    } 
}
