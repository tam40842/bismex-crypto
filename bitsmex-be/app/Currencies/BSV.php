<?php
namespace App\Currencies;

use App\Coinpayments;
use Illuminate\Support\Facades\Cache;

class BSV {
    public $symbol;

    public function __construct() {
        $this->coinpayments = new Coinpayments();
        $this->symbol = 'BSV';
    }

    public function account() {
		$list = $this->coinpayments->call('balances', array('all' => 1));
        $status = ($list['error'] == 'ok') ? array('status' => true, 'api_status' => $list['result'][$this->symbol]['status'], 'coin_status' => $list['result'][$this->symbol]['coin_status'], 'symbol' => $this->symbol, 'balance' => $list['result'][$this->symbol]['balancef']) : array('status' => false);
        return json_encode($status);
    }
    
    public function generateAddress($label = '') {
		$get = $this->coinpayments->call('get_callback_address', array('currency' => $this->symbol, 'ipn_url' => route('api.coinpayments')));
        $status = ($get['error'] == 'ok') ? array('status' => true, 'symbol' => $this->symbol, 'address' => $get['result']['address'], 'callback' => route('api.coinpayments')) : array('status' => false);
        return json_encode($status);
    }

	public function validator($address) {
        $validate = preg_match('/^[1,3][a-zA-Z1-9]{32,34}$/i', $address);
        return $validate;
    }
    
    public function rate() {
        try {
            $get = @json_decode(@file_get_contents('https://api.binance.com/api/v1/ticker/24hr?symbol=BCHUSDT'));
            return floatval(@$get->bidPrice);
        } catch (Exception $e) {
            $price = 0;
        }
        return $price;
    }
    
    public function percent_change() {
        try {
            $get = json_decode(file_get_contents('https://api.binance.com/api/v1/ticker/24hr?symbol=BCHUSDT'));
            return $get;
        } catch (Exception $e) {
            
        }
        return false;
    }
    
    public function transactionLink($address) {
		return 'https://blockchain.info/address/'.$address;
    }

    public function hashLink($tx_id) {
        $url = 'https://www.blockchain.com/bch/tx/'.$tx_id;
        return $url;
    }    
    
    public function addressLink($address) {
        $url = 'https://www.blockchain.com/bch/address/'.$address;
        return $url;
    }    
    
	public function transfer($address, $amount, $memo) {
		$get = $this->coinpayments->call('create_withdrawal', array('amount' => $amount, 'currency' => $this->symbol, 'address' => $address, 'auto_confirm' => 1, 'note' => $memo));
		$status = ($get['error'] == 'ok') ? array('status' => true, 'symbol' => $this->symbol, 'address' => $address, 'description' => $memo, 'amount' => $amount, 'response' => json_encode($get)) : array('status' => false, 'msg' => $get);
		return json_encode($status);
	}
}
