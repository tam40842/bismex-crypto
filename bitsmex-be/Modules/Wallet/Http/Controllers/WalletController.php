<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use DB;
use Validator;
use Auth;
use App\Twofa;
use Modules\Wallet\Entities\Transfer;
use Modules\Wallet\Entities\Withdraw;
use Modules\Wallet\Entities\TransferWallet;
use Modules\Wallet\Entities\WalletAddress;
use App\Http\Controllers\Vuta\CryptoMap;
use App\Http\Controllers\Vuta\Status;
use App\Http\Controllers\Vuta\Vuta;
use Modules\TelegramBot\Entities\Telegram;
use Modules\AntiCheat\Entities\Anti;
use App\Jobs\SendEmail;
use App\Jobs\Exchange;
use Carbon\Carbon;
use App\TransactionHistory;

class WalletController extends Controller
{
    use CryptoMap, Status, Vuta;

    public $from_balance = ['live', 'wallet', 'bonus'];
    public $to_balance = ['live', 'wallet'];

    public function getCurrencies()
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $currencies = DB::table('currencies')->where('actived', 1)->get();
        $n = 0;
        do {
            $user_balance = DB::table('user_balance')->where('userid', $user->id)->first();
            if (is_null($user_balance)) {
                DB::table('user_balance')->insert(['userid' => $user->id]);
            }
        } while (is_null($user_balance));

        foreach ($currencies as $key => $value) {
            do {
                $wallet = DB::table('wallet_address')->where('userid', $user->id)->where('symbol', $value->symbol)->first();
                if (is_null($wallet)) {
                    $count = DB::table('wallet_address')->count();
                    $coin = 'App\\Currencies\\' . $value->symbol;
                    $coin = new $coin;
                    $generate = $coin->generateAddress($count);
                    $generate = json_decode($generate, true);
                    if ($generate['status']) {
                        DB::table('wallet_address')->where('userid', $user->id)->insert([
                            'userid' => $user->id,
                            'symbol' => $value->symbol,
                            'input_address' => $generate['address'],
                            'stt' => $count,
                            'created_at' => date(now()),
                            'updated_at' => date(now()),
                        ]);
                        $user->admin_setup ? $currencies[$key]->input_address = '' : $currencies[$key]->input_address = $generate['address'];
                        // $currencies[$key]->input_address = $generate['address'];
                    }
                } else {
                    $user->admin_setup ? $currencies[$key]->input_address = '' : $currencies[$key]->input_address = $wallet->input_address;
                    // $currencies[$key]->input_address = $wallet->input_address;
                }
                $n++;
            } while (is_null($wallet) && $n < 3);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'data' => [
                'currencies' => $currencies,
                'balance' => $user_balance
            ]
        ]);
    }

    public function postOverview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_balance' => 'required',
            'to_balance' => 'required',
            'amount' => 'required|numeric|min:1',
            'twofa_code' => 'required_if:to_balance,live_balance|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'This account has been banned or deactived.'
                ]);
            }

            $twofa = new Twofa();
            $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
            if (!$valid) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The Two-factor Authentication code is invalid.'
                ]);
            }

            $total_balance = ['live_balance', 'primary_balance', 'autotrade_balance'];
            $from_balance = $request->from_balance;
            $to_balance = $request->to_balance;
            $amount = $request->amount;
            if (!in_array($from_balance, $total_balance) || !in_array($to_balance, $total_balance)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Balance not found'
                ]);
            }
            if ($amount > $user->{$from_balance}) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your balance is not enough.'
                ]);
            }
            $randomCodeTransaction = strtoupper(uniqid('TO'));
            // Save transaction history
            TransactionHistory::historyLiveBalance($user->id, 'OVERVIEW', $amount * -1, $from_balance, $randomCodeTransaction);
            TransactionHistory::historyLiveBalance($user->id, 'OVERVIEW', $amount, $to_balance, $randomCodeTransaction);

            DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement($from_balance, $amount);
            DB::table('users')->where('id', $user->id)->lockForUpdate()->increment($to_balance, $amount);
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Balance conversion successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Overview has error',
            ]);
        }
    }

    public function postTransactionsAccount()
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }

        $transaction = DB::table('transactions')
            ->where('userid', $user->id)
            ->where('wallet_type', 'primary_balance')
            ->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => [
                'transaction' => $transaction
            ]
        ]);
    }

    public function getHistoriesWallet(Request $request)
    {
        $user = Auth::user();
        $historiesWallet = DB::table('deposit')->where('userid', $user->id)
            ->join('withdraw', 'deposit.userid', '=', 'withdraw.userid')
            ->join('transfers', 'deposit.userid', '=', 'transfers.userid')
            ->orderBy('deposit.created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $historiesWallet,
        ]);
    }

    public function postSearchDeposit(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $deposits = DB::table('deposit as d')->leftjoin('wallet_address as w', 'd.symbol', '=', 'w.symbol')->where('d.userid', $user->id)->where('w.userid', $user->id)->where('d.status', 1)->whereBetween('d.created_at', [$request->start, $request->end])->select('w.input_address as address', 'd.*')->paginate(5);
        $deposit_status = $this->deposit_status();
        foreach ($deposits as $key => $value) {
            $deposits[$key] = $value;
            $deposits[$key]->status_html = strip_tags($deposit_status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $deposits
        ]);
    }

    public function postSearchWithdraw(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $withdraw = DB::table('withdraw')->where('userid', $user->id)->whereBetween('created_at', [$request->start, $request->end])->orderBy('id', 'desc')->paginate(5);
        $status = $this->withdraw_status();
        foreach ($withdraw as $key => $value) {
            $withdraw[$key] = $value;
            $withdraw[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $withdraw
        ]);
    }

    public function postSearchTransfer(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $transfers = DB::table('transfers as t')->leftjoin('users as s', 't.userid', '=', 's.id')->leftjoin('users as r', 't.recipient_id', '=', 'r.id')->where(function ($query) use ($user) {
            $query->where('t.userid', $user->id)->orWhere('t.recipient_id', $user->id);
        })->whereBetween('t.created_at', [$request->start, $request->end])->where('t.status', 1)->select('t.*', 's.username as sender', 'r.username as receiver')->orderBy('t.id', 'desc')->paginate(5);
        $status = $this->transfers_status();
        foreach ($transfers as $key => $value) {
            $transfers[$key] = $value;
            $transfers[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $transfers
        ]);
    }

    public function postSearchConvert(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $histories = DB::table('convert')->where('userid', $user->id)->whereBetween('created_at', [$request->start, $request->end])->paginate(5);
        $status = $this->convert_status();
        foreach ($histories as $key => $value) {
            $histories[$key] = $value;
            $histories[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data convert is success',
            'data' => $histories
        ]);
    }
    public function postDepositHistories(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->where('admin_setup', 0)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }
        $deposits = DB::table('deposit as d')->leftjoin('wallet_address as w', 'd.symbol', '=', 'w.symbol')
            ->leftjoin('currencies as c', 'd.symbol', '=', 'c.symbol')
            ->where('d.userid', $user->id)->where('w.userid', $user->id)->where('d.status', 1);

        if (isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from);
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to) : Carbon::now();
            $deposits = $deposits->whereDate('d.created_at', '>=', $date_start)->whereDate('d.created_at', '<=', $date_end);
        }
        $deposits = $deposits->select('w.input_address as address', 'd.*', 'c.logo')->orderBy('id', 'desc')->paginate(10);

        // $deposit_status = $this->deposit_status();
        // foreach($deposits as $key => $value) {
        //     $deposits[$key] = $value;
        //     $deposits[$key]->status_html = strip_tags($deposit_status[$value->status]);
        // }
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $deposits
        ]);
    }

    public function postTransfer(Request $request)
    {
        // return response()->json([
        //     'status' => 422,
        //     'message' => 'Access Denied.',
        // ]);
        
        $validator = Validator::make($request->all(), [
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:5',
            'twofa_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        DB::beginTransaction();
        try {
            //kiểm tra user admin-setup
            //nếu là user (admin-setup thì chỉ được chuyển trong nhánh admin-setup)
            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->where('admin_setup', 0)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'This account has been banned or deactived.'
                ]);
            }
            $twofa = new Twofa();
            $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
            if (!$valid) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The Two-factor Authentication code is invalid.'
                ]);
            }
            // $recipient = DB::table('users')->where(function($query) use ($request) {
            //     $query->where('email', $request->recipient)->orWhere('username', $request->recipient);
            // })->first();

            $wallet_recipient = DB::table('wallet_address')->where('input_address', $request->recipient)->first();
            if (is_null($wallet_recipient)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The transfer address does not exist.'
                ]);
            }

            $recipient = DB::table('users')->where('id', $wallet_recipient->userid)->where('admin_setup', 0)->where('status', 1)->lockForUpdate()->first();
            if (is_null($recipient)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The transfer does not exist.'
                ]);
            }
            // //user admin-setup
            // if($user->admin_setup != $recipient->admin_setup) {
            //     return response()->json([
            //         'status' => 422,
            //         'message' => 'Recipient not found.'
            //     ]); 
            // }

            // // kiểm tra chỉ cho phép chuyển trong nhánh
            // $user_branch = DB::table('users')->where('id', $user->id)->where('sponsor_code', 'LIKE', '%'. $recipient->sponsor_ref.'%')->first();
            // $recipient_branch = DB::table('users')->where('id', $recipient->id)->where('sponsor_code', 'LIKE', '%'. $user->sponsor_ref.'%')->first();

            // if(is_null($user_branch) && is_null($recipient_branch)) {
            //     return response()->json([
            //         'status' => 422,
            //         'message' => 'You cannot switch recipients that are not in the branch.'
            //     ]);
            // }

            if ($recipient->id == $user->id) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Can not transfer to yourself'
                ]);
            }

            $amount = abs((float)$request->amount);
            if ($amount > $user->primary_balance || $amount <= 0) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your balance is not enough.'
                ]);
            }
            //kiểm tra giới hạn chuyển tiền trong hệ thống
            $settings = $this->get_settings(['transfer_limit']);
            $transfer_limit = $settings['transfer_limit'];
            $transfer_limit = explode(';', $transfer_limit);
            $transfer_min = $transfer_limit[0];
            $transfer_max = $transfer_limit[1];
            if ($amount < $transfer_min || $amount > $transfer_max) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Transfers range from $' . number_format($transfer_min, 2) . ' to $' . number_format($transfer_max, 2),
                ]);
            }

            // $settings = $this->get_settings(['transfer_limit']);
            // $transfer_limit = explode(';', $settings['transfer_limit']);
            // if($transfer_limit[0] > $amount || $amount > $transfer_limit[1]) {
            //     return response()->json([
            //         'status' => 422,
            //         'message' => 'You can only transfer between $'.$transfer_limit[0].' - '.'$'.$transfer_limit[1]
            //     ]);
            // }

            $data = [
                'action' => 'TRANSFER',
                'transfer_id' => strtoupper(uniqid('T')),
                'userid' => $user->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'created_at' => date(now()),
                'updated_at' => date(now()),
            ];
            $settings = $this->get_settings(['transfer_fee']);
            $data['fee'] = $amount * $settings['transfer_fee'] / 100;
            $data['total'] = $amount - $data['fee'];
            
            if($amount > 1000){
                $data['status'] = 0;
                // Save transaction history
                TransactionHistory::historyLiveBalance($user->id, 'TRANSFER', $amount * -1, 'primary_balance', 'TRANSFER ID: ' . $data['transfer_id']);
                DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement('primary_balance', $amount);
            } else{
                // Save transaction history
                TransactionHistory::historyLiveBalance($user->id, 'TRANSFER', $amount * -1, 'primary_balance', 'TRANSFER ID: ' . $data['transfer_id']);
                TransactionHistory::historyLiveBalance($recipient->id, 'TRANSFER', $data['total'], 'primary_balance', 'TRANSFER ID: ' . $data['transfer_id']);

                DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement('primary_balance', $amount);
                DB::table('users')->where('id', $recipient->id)->lockForUpdate()->increment('primary_balance', $data['total']);
                $data['status'] = 1;
                Anti::firewall($user->id, 'transfer', [
                    'user' => $user,
                    'amount' => $amount,
                    'recipient_id' => $recipient->id,
                    'created_at' => date(now())
                ]);
                SendEmail::dispatch($user->email, 'You have just transferred money to another account', 'transfer', [
                    'user' => $user,
                    'amount' => $amount,
                    'created_at' => $data['created_at'],
                    'receiver' => $recipient->username
                ]);
                SendEmail::dispatch($recipient->email, 'You have just received a money transfer', 'received', [
                    'user' => $recipient,
                    'amount' => $data['total'],
                    'created_at' => $data['created_at'],
                    'sender' => $user->username
                ]);
            }

            $user_sender = DB::table('users')->where('id', $user->id)->select('primary_balance')->first();
            $user_recipient = DB::table('users')->where('id', $recipient->id)->select('primary_balance')->first();
            $data['user_balance'] = $user_sender->primary_balance;
            $data['recipient_balance'] = $user_recipient->primary_balance;
            DB::table('transfers')->insert($data);
            DB::commit();
            

            if($amount > 1000){
                return response()->json([
                    'status' => 200,
                    'message' => 'You have a transfer $' . number_format($amount, 2) . ' to ' . $recipient->username . ' has been successfully. Transfer amount greater than $1000 needs to wait for admin approval.',
                ]);
            } else{
                return response()->json([
                    'status' => 200,
                    'message' => 'You have a transfer $' . number_format($amount, 2) . ' to ' . $recipient->username . ' has been successfully.',
                ]);
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Transfer has error',
            ]);
        }
        return response()->json([
            'status' => 422,
            'message' => 'Access Denied.',
        ]);
    }

    public function postTransferHistories(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }
        $transfers = DB::table('transfers as t')
        ->leftjoin('wallet_address as s', 't.userid', '=', 's.userid')
        ->leftjoin('wallet_address as r', 't.recipient_id', '=', 'r.userid')
        ->leftjoin('users as ru', 't.recipient_id', '=', 'ru.id')
        ->where(function ($query) use ($user) {
            $query->where('t.userid', $user->id)->orWhere('t.recipient_id', $user->id);
        });
        if (isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from);
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to) : Carbon::now();
            $transfers = $transfers->whereDate('t.created_at', '>=', $date_start)->whereDate('t.created_at', '<=', $date_end);
        }
        $transfers = $transfers->select(
            's.input_address as user_address',
            'r.input_address as receiver_address',
            'ru.username as username',
            't.amount as amount',
            't.user_balance',
            't.recipient_balance',
            't.created_at as created_at',
            't.status',
            DB::raw("case when s.userid = '" . $user->id . "' then 'sender' else 'receiver' end as role")
        )->orderBy('t.created_at', 'desc')->paginate(10);
        $status = $this->transfers_status();
        foreach($transfers as $key => $value) {
            $transfers[$key] = $value;
            $transfers[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $transfers
        ]);
    }

    public function postWithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:21',
            'twofa_code' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if (!$valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The Two-factor Authentication code is invalid.'
            ]);
        }
        $symbol = strtoupper($request->symbol);
        $address = $request->address;
        $address_valid = $this->addressValidate($symbol, $address);
        if (!$address_valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The wallet address is invalid.'
            ]);
        }

        $amount = abs((float)$request->amount);
        // $user_balance = DB::table('user_balance')->where('userid', $user->id)->first();
        // if(is_null($user_balance)) {
        //     return response()->json([
        //         'status' => 422,
        //         'message' => 'Withdraw has an error.'
        //     ]);
        // }
        $balance = $user->primary_balance;
        if ($amount > $balance || $amount <= 0) {
            return response()->json([
                'status' => 422,
                'message' => 'Your wallet balance is not enough.'
            ]);
        }

        $currency = DB::table('currencies')->where('actived', 1)->where('symbol', $symbol)->first();
        if (is_null($currency)) {
            return response()->json([
                'status' => 422,
                'message' => 'The payment method does not exist.'
            ]);
        }

        if ($amount > $currency->withdraw_max) {
            return response()->json([
                'status' => 422,
                'message' => 'The amount your withdraw max is ' . $currency->withdraw_max . ' ' . $currency->symbol
            ]);
        }

        if ($amount < $currency->withdraw_min) {
            return response()->json([
                'status' => 422,
                'message' => 'The amount your withdraw min is ' . $currency->withdraw_min . ' ' . $currency->symbol
            ]);
        }

        $data = [
            'action' => 'WITHDRAW',
            'withdraw_id' => strtoupper(uniqid('W')),
            'userid' => $user->id,
            'symbol' => $symbol,
            'output_address' => $address,
            'amount' => $amount,
            'created_at' => date(now()),
            'updated_at' => date(now())
        ];
        $data['fee'] = $currency->withdraw_fee;
        $data['rate'] = $currency->usd_rate;
        $data['total'] = $amount / $data['rate'] - $data['fee'];
        if ($data['total'] < $currency->withdraw_min) {
            return response()->json([
                'status' => 422,
                'message' => 'The total receive min is ' . $data['total'] . ' ' . $currency->symbol
            ]);
        }
        if ($data['total'] < $currency->withdraw_min) {
            return response()->json([
                'status' => 422,
                'message' => 'The total receive max is ' . $data['total'] . ' ' . $currency->symbol
            ]);
        }
        DB::beginTransaction();
        try {
            DB::table('withdraw')->insert($data);
            // add Transaction history
            TransactionHistory::historyLiveBalance($user->id, 'WITHDRAW', $amount * -1, 'primary_balance', 'WITHDRAW ID: ' . $data['withdraw_id']);

            DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement('primary_balance', $amount);
            DB::commit();
            Anti::firewall($user->id, 'withdraw', [
                'user' => $user,
                'amount' => $amount,
                'address' => $address,
                'created_at' => date(now())
            ]);
            SendEmail::dispatch($user->email, 'Your withdraw order is being processed', 'withdraw', [
                'user' => $user,
                'amount' => $amount,
                'symbol' => $symbol,
                'output_address' => $address,
                'fee' => $data['fee']
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'You have a withdraw ' . round($amount, 2) . ' ' . $symbol . '. Your withdraw has been processing.',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Withdraw has error',
            ]);
        }
        return response()->json([
            'status' => 422,
            'message' => 'Access Denied.',
        ]);
    }

    public function postWithdrawHistories(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }
        $withdraw = DB::table('withdraw')
            ->leftJoin('currencies', 'withdraw.symbol', '=', 'currencies.symbol')
            ->where('withdraw.userid', $user->id);
        if (isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from);
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to) : Carbon::now();
            $withdraw = $withdraw->whereDate('withdraw.created_at', '>=', $date_start)->whereDate('withdraw.created_at', '<=', $date_end);
        }
        $withdraw = $withdraw->select('withdraw.*', 'currencies.logo')
            ->orderBy('withdraw.id', 'desc')->paginate(10);

        $status = $this->withdraw_status();
        foreach ($withdraw as $key => $value) {
            $withdraw[$key] = $value;
            $withdraw[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $withdraw
        ]);
    }

    public function WalletTransfer(Request $request)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Access deny.'
        ]);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'from_wallet' => 'required|string',
            'to_wallet' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        $user = DB::table('users')->where('status', 1)->lockForUpdate()->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        if (!in_array($request->from_wallet, $this->from_balance)) {
            return response()->json([
                'status' => 422,
                'message' => 'The from wallet does not exist.'
            ]);
        }

        if (!in_array($request->to_wallet, $this->to_balance)) {
            return response()->json([
                'status' => 422,
                'message' => 'The to wallet does not exist.'
            ]);
        }

        if ($request->from_wallet == $request->to_wallet) {
            return response()->json([
                'status' => 422,
                'message' => 'Access denied.'
            ]);
        }
        $amount = abs((float)$request->amount);
        $from_balance_type = $request->from_wallet . '_balance';
        $to_balance_type = $request->to_wallet . '_balance';
        $balance = $user->{$from_balance_type};
        if ($amount > $balance && $amount > 0) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance does not enough.'
            ]);
        }
        try {
            DB::table('wallet_transfers')->insert([
                'userid' => $user->id,
                'amount' => $amount,
                'from_wallet' => $request->from_wallet,
                'to_wallet' => $request->to_wallet,
                'status' => 1,
                'created_at' => date(now()),
                'updated_at' => date(now())
            ]);
            DB::table('users')->where('id', $user->id)->decrement($from_balance_type, $amount);
            DB::table('users')->where('id', $user->id)->increment($to_balance_type, $amount);
            DB::commit();
            Anti::firewall($user->id, 'wallet_transfer', [
                'from_balance_type' => $from_balance_type,
                'to_balance_type' => $to_balance_type,
                'user' => $user,
                'amount' => $amount,
                'created_at' => date(now())
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Transfer has error',
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'The balance has been transfered.'
        ]);
    }

    public function getwallettransferHistories()
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $transfers = TransferWallet::where('userid', $user->id)->where('status', 1)->orderBy('id', 'desc')->paginate(5);
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $transfers
        ]);
    }

    public function ExchangeHistories()
    {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $histories = DB::table('exchange')->leftJoin('currencies', 'exchange.symbol', '=', 'currencies.symbol')->where('exchange.userid', $user->id)->select('exchange.*', 'currencies.logo')->orderBy('id', 'desc')->paginate(5);
        $status = $this->exchange_status();
        foreach ($histories as $key => $value) {
            $histories[$key] = $value;
            $histories[$key]->status_html = strip_tags($status[$value->status]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data exchange is success',
            'data' => $histories
        ]);
    }

    public function exchange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'is_swap' => 'required|boolean',
            'twofa_code' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'This account has been banned or deactived.'
                ]);
            }
            $twofa = new Twofa();
            $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
            if (!$valid) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The Two-factor Authentication code is invalid.'
                ]);
            }
            $symbol = strtoupper($request->symbol);
            $currency = DB::table('currencies')->where('actived', 1)->where('symbol', $symbol)->first();
            if (is_null($currency)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The currency does not exist.'
                ]);
            }

            $amount = abs((float)$request->amount);
            $user_balance = DB::table('user_balance')->where('userid', $user->id)->first();
            if (is_null($user_balance)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Exchange has an error.'
                ]);
            }

            $is_swap = (int)$request->is_swap;
            $balance = !$is_swap ? $user_balance->{$symbol} : $user->live_balance;

            if ($amount > $balance || $amount <= 0) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your balance is not enough.'
                ]);
            }

            $data = [
                'userid' => $user->id,
                'is_swap' => $is_swap,
                'symbol' => $symbol,
                'amount' => $amount,
                'status' => 1,
            ];
            $data['rate'] = CryptoMap::currencyRate($symbol);
            $data['total'] = !$is_swap ? $amount * $data['rate'] : $amount / $data['rate'];
            if (!$is_swap) {
                # From crypto to USD
                if ($amount < $currency->min_swap) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Swap amount min is: ' . $currency->min_swap . ' ' . $symbol
                    ]);
                }
                if ($amount > $currency->max_swap) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Swap amount max is: ' . $currency->max_swap . ' ' . $symbol
                    ]);
                }
            } else {
                # From USD to any Crypto
                if ($data['total'] < $currency->min_swap) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Swap amount min is: ' . $currency->min_swap . ' ' . $symbol
                    ]);
                }
                if ($data['total'] > $currency->max_swap) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Swap amount max is: ' . $currency->max_swap . ' ' . $symbol
                    ]);
                }
            }
            $data['total'] = round($data['total'], 8);

            DB::table('exchange')->insert($data);
            if (!$is_swap) {
                # if change from currency to USD
                DB::table('user_balance')->where('userid', $user->id)->decrement($symbol, $amount);
                DB::table('users')->where('id', $user->id)->increment('live_balance', $data['total']);
            } else {
                # if exchange from USD to currency
                DB::table('user_balance')->where('userid', $user->id)->increment($symbol, $data['total']);
                DB::table('users')->where('id', $user->id)->decrement('live_balance', $amount);
            }
            DB::commit();
            Exchange::dispatch($symbol, $amount, $is_swap);
            // Anti::firewall($user->id, 'wallet_transfer', [
            //     'from_balance_type' => $from_balance_type,
            //     'to_balance_type' => $to_balance_type,
            //     'user' => $user,
            //     'amount' => $amount,
            //     'created_at' => date(now())
            // ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Exchange has error',
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Your balance has been converted'
        ]);
    }

    public function getDemoBalance()
    {
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'This account has been banned or deactived.'
                ]);
            }
            DB::table('users')->where('id', $user->id)->lockForUpdate()->update([
                'demo_balance' => 1000,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Get more success.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Get more has errors.'
            ]);
        }
    }
}
