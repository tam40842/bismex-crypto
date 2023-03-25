<?php

namespace Modules\Callback\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Jobs\SendEmail;
use App\Jobs\Exchange;
use App\Coinbase;
use DB;
use Modules\TelegramBot\Entities\Telegram;
use App\User;
use App\TransactionHistory;

class CallbackController extends Controller
{

    public function coinbase(Request $request)
    {
        // $coinbase = new Coinbase();
        // $raw_data = file_get_contents('php://input');
        // $signature = @request()->server('HTTP_CB_SIGNATURE');
        // $verifyCallback = $coinbase->verifyCallback($raw_data, $signature);
        // $data = json_decode($raw_data);
        // if($verifyCallback) {
        //     $tx_id = $data->additional_data->hash;
        //     $symbol = strtoupper($data->additional_data->amount->currency);
        //     if($symbol == 'BTC') {
        //         return false;
        //     }
        //     $input_address = $data->data->address;
        //     $amount = $data->additional_data->amount->amount;
        //     $transactions = DB::table('transactions')->where('transfer_from', $symbol)->whereIn('status', [0, 3])->where('input_address', $input_address)->whereNull('txhash')->where('action', 'sell')->first();

        //     if(!is_null($transactions)) {
        //         $transaction_data = [
        //             'rate' => $transactions->rate,
        //             'amount' => $transactions->amount,
        //             'amount_convert' => $transactions->amount_convert,
        //             'current_rate' => $transactions->current_rate,
        //             'current_amount' => $transactions->current_amount,
        //             'current_amount_convert' => $transactions->current_amount_convert,
        //         ];
        //         $currencies_rate = DB::table('currencies_rate')->where('symbol', $symbol)->first();

        //         if($transactions->amount != $amount) {
        //             $transaction_data['amount'] = $amount;
        //             $transaction_data['current_amount'] = $transactions->amount;
        //             $transaction_data['amount_convert'] = round($transaction_data['rate'] * $amount);
        //         }
        //         if($currencies_rate->bid < $transactions->rate) {
        //             $transaction_data['rate'] = $currencies_rate->bid;
        //             $transaction_data['current_rate'] = $transactions->rate;
        //             $transaction_data['current_amount_convert'] = $transactions->amount_convert;

        //             $transaction_data['amount_convert'] = round($transaction_data['rate'] * $transaction_data['amount']);
        //         }
        //         $transaction_data['status'] = 1;
        //         $transaction_data['txhash'] = $tx_id;
        //         DB::table('transactions')->where('id', $transactions->id)->update($transaction_data);
        //         $status = Vuta::transaction_status();
        //         $push_data = [
        //             'transaction' => [
        //                 'orderid' => $transactions->orderid,
        //                 'currency' => $transactions->transfer_from,
        //                 'payment' => $transactions->receive_to,
        //                 'amount' => $transaction_data['amount'],
        //                 'amount_convert' => round($transaction_data['amount_convert']),
        //                 'rate' => $transaction_data['rate'],
        //                 'current_rate' => $transaction_data['current_rate'],
        //             ],
        //             'status_html' => $status[1],
        //             'remove_payment_info' => true,
        //             'review_form' => '',
        //         ];

        //         Transfer::dispatch($transactions->orderid, $transactions->action , $transactions->receive_to, $transactions->receive_account_number, $transaction_data['amount_convert'], $transactions->orderid);
        //         event(new Transaction(json_encode($push_data), $transactions->orderid));
        //     }
        // }
    }

    public function coinpayments(Request $request)
    {
        $data = json_encode($request->all());
        DB::table('settings')->where('setting_name', 'request_coinpayment')->update(['setting_value' => $data]);
        if ($request->has('currency')) {
            $symbol = strtoupper($request->currency);
            $symbol = ($symbol == 'USDT.ERC20') ? 'USDT' : $symbol;
            if ($symbol == 'USDT') {
                $txn_id = $request->txn_id;
                $input_address = $request->address;
                $amount = (float)$request->amount;
                $check_exist = DB::table('deposit')->where('txhash', $txn_id)->first();
                if (is_null($check_exist)) {
                    $wallet_address = DB::table('wallet_address')->where('symbol', $symbol)->where('input_address', $input_address)->first();
                    if (!is_null($wallet_address)) {
                        $userid = $wallet_address->userid;
                        $user = User::find($userid);
                        $currency = '\\App\\Currencies\\' . $symbol;
                        $currency = new $currency;
                        $rate = $currency->rate();
                        $total = $amount * $rate;
                        $create = [
                            'deposit_id' => strtoupper(uniqid('D')),
                            'action' => 'DEPOSIT',
                            'userid' => $userid,
                            'symbol' => $symbol,
                            'amount' => $amount,
                            'rate' => $rate,
                            'total' => $total,
                            'status' => 1,
                            'txhash' => $txn_id,
                            'type' => 'deposit',
                            'created_at' => date(now()),
                            'updated_at' => date(now())
                        ];
                        DB::table('deposit')->insert($create);

                        // Save transaction history
                        TransactionHistory::historyLiveBalance($userid, 'DEPOSIT', $total, 'primary_balance', 'COINPAYMENTS: ' . $create['deposit_id']);

                        DB::table('users')->where('id', $userid)->increment('primary_balance', $total);
                        Telegram::handle($userid, 'deposit', [
                            'user' => $user,
                            'amount' => $amount,
                        ]);
                        SendEmail::dispatch($user->email, 'Your account has just been credited', 'deposit', ['user' => $user, 'amount' => $total]);
                    }
                }
            }
        }
    }

    public function bep20(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->data;
            $secretKey = $request->secret_key;
            if ($secretKey != config('app.callback_key')) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Has an error.'
                ]);
            }
            $wallet = DB::table('wallet_address')->where('input_address', $data['to'])->first();
            if (is_null($wallet)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Wallet is not exist.'
                ]);
            }

            // check txn hash
            $txn = DB::table('deposit')->where('txhash', $data['txn_hash'])->first();
            if (!is_null($txn)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Deposit is duplicate.'
                ]);
            }

            if ($wallet->userid == 1) {
                return;
            }

            $rate = 1;
            $fee = 0;
            $create = [
                'deposit_id' => strtoupper(uniqid('D')),
                'action' => 'DEPOSIT',
                'userid' => $wallet->userid,
                'symbol' => 'USDT',
                'amount' => $data['amount'],
                'rate' => $rate,
                'total' => $data['amount'] * $rate,
                'stt' => $wallet->stt,
                'status' => 1,
                'txhash' => $data['txn_hash'],
                'address' => $data['to'],
                'type' => 'deposit',
                'created_at' => date(now()),
                'updated_at' => date(now())
            ];
            DB::table('deposit')->insert($create);
            $user = DB::table('users')->where('id', $create['userid'])->lockForUpdate()->first();
            // Save transaction history
            TransactionHistory::historyLiveBalance($create['userid'], 'DEPOSIT', $create['total'], 'primary_balance', 'BitsMex Node: ' . $create['deposit_id']);
            DB::table('users')->where('id', $create['userid'])->lockForUpdate()->increment('primary_balance', $create['total']);

            Telegram::handle($user->id, 'deposit', [
                'user' => $user,
                'amount' => $data['amount'],
            ]);
            SendEmail::dispatch($user->email, 'Your account has just been credited', 'deposit', ['user' => $user, 'amount' => $create['total']]);
            DB::commit();
            file_get_contents('https://bep20.bitsmex.net/api/v1/forward/' . $wallet->stt);
            return response()->json([
                'status' => 200,
                'message' => 'Deposit is success.'
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Has an error'
            ]);
        }
    }
}
