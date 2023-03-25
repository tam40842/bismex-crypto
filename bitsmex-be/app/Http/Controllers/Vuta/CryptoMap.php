<?php
namespace App\Http\Controllers\Vuta;

trait CryptoMap {
    
    public static function get_system_balance($symbol, $base_address) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $account = $currency->account($base_address);
        return $account;
    }
    public static function addressValidate($symbol, $address) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $validator = $currency->validator($address);
        return $validator;
    }
    public static function generateAddress($symbol) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $address = $currency->generateAddress();
        return $address;
    }
    public static function currencyRate($symbol) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $rate = $currency->rate();
        return $rate;
    }
    public static function percent_change($symbol) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $percent_change = $currency->percent_change();
        return $percent_change;
    }
    public static function transactionLink($symbol, $address) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $transactionLink = $currency->transactionLink($address);
        return $transactionLink;
    }
    public static function hashLink($symbol, $tx_id) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $hashLink = $currency->hashLink($tx_id);
        return $hashLink;
    }

    public static function addressLink($symbol, $tx_id) {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $hashLink = $currency->addressLink($tx_id);
        return $hashLink;
    }

    public static function transfers($amount, $address, $memo, $symbol = "USDT") {
        $symbol = 'App\\Currencies\\'.strtoupper($symbol);
        $currency = new $symbol;
        $transfer = $currency->transfer($amount, $address, $memo);
        return $transfer;
    }
}