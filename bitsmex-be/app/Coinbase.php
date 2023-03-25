<?php
namespace App;

use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Account;
use Coinbase\Wallet\Resource\Address;
use Coinbase\Wallet\Enum\CurrencyCode;
use Coinbase\Wallet\Resource\Transaction;
use Coinbase\Wallet\Value\Money;
use Coinbase\Wallet\Enum\Param;
use Coinbase\Wallet\Exception\TwoFactorRequiredException;
use Coinbase\Wallet\Exception\ClientException;
use Coinbase\Wallet\Exception\ValidationException;

class Coinbase {
	public function __construct() {
		$configuration = Configuration::apiKey(env("COINBASE_API_KEY"), env("COINBASE_API_SECRET"));
		$this->client = Client::create($configuration);
	}

	// get list accounts
	public function getAccounts() {
		$result = array();
		$accounts = $this->client->getAccounts();
		foreach ($accounts as $key => $account)
			$result[] = $account->getRawData();
		return $result;
	}

	// get 1 account
	public function getAccount($accountId) {
		$result = array();
		$account = $this->client->getAccount($accountId);
		return $account->getRawData();
	}

	// get list address
	public function getAddresses($accountId) {
		$result = array();
		$account = $this->client->getAccount($accountId);
		$addresses = $this->client->getAccountAddresses($account);
		foreach ($addresses as $key => $address)
			$result[] = $address->getRawData();
		return $result;
	}

	// get 1 address
	public function getAddress($accountId, $addressId) {
		$account = $this->client->getAccount($accountId);
		$address = $this->client->getAccountAddress($account, $addressId);
		return $address->getRawData();
	}

	// get list transaction Account
	public function getTransactionsAccount($accountId) {
		$account = $this->client->getAccount($accountId);
		$transactions = $this->client->getAccountTransactions($account);
		return $transactions;
	}
	
	// get list transaction Wallet
	public function getTransactionsAddress($accountId, $addressId) {
			$result = array();
			$account = $this->client->getAccount($accountId);
			$address = $this->client->getAccountAddress($account, $addressId);
			$transactions = $this->client->getAddressTransactions($address);
			foreach ($transactions as $key => $transaction) {
				$result[] = $transaction->getRawData();
			}
			return $result;
		}
	
	// create wallet
	public function createWallet($accountId, $nameWallet, $callback_url) {
		$account = $this->client->getAccount($accountId);
		$address = new Address([
			'name' => $nameWallet
		]);
		$this->client->createAccountAddress($account, $address, array('callback_url' => $callback_url));
		return $address->getRawData();
	}

	public function getRate($ecurrency) {
		switch($ecurrency) {
			case 'BTC': $rates = $this->client->getSellPrice("BTC-USD"); break;
			case 'ETH': $rates = $this->client->getBuyPrice("ETH-USD"); break;
			case 'LTC': $rates = $this->client->getSellPrice("LTC-USD"); break;
		}
		return $rates->getAmount();
	}
	
	public function rate_to_btc($symbol) {
		$get = file_get_contents('https://api.coinbase.com/v2/exchange-rates?currency='.$symbol);
		$data = json_decode($get);
		return $data->data->rates->BTC;
	}
	
	//send funds - chuyển tiền
	public function sendFunds($accountId, $amount, $toWallet, $symbol, $memo) {
		$rate = $this->rate_to_btc(strtoupper($symbol));
		$sendAmount = round(($amount * $rate), 8);
		$transaction = Transaction::send([
			'toEmail' => $toWallet,
			'amount' => new Money($sendAmount, CurrencyCode::BTC),
			'description' => $memo
		]);
		
		$account = $this->client->getAccount($accountId);
		
		try {
			$this->client->createAccountTransaction($account, $transaction);
			return $transaction->getrawData();
		} catch (Exception $e) {
			$getTrace = $e->getTrace();
			return $getTrace[2]['args'][0];
		} catch (TwoFactorRequiredException $e) {
			return false;
		}
	}
	
	public function verifyCallback($raw_body, $signature) {
		$authenticity = $this->client->verifyCallback($raw_body, $signature);
		return $authenticity;
	}
}
