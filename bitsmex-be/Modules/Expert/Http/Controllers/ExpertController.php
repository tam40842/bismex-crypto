<?php

namespace Modules\Expert\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Auth;
use DB;
use Carbon\Carbon;
use Validator;
use App\Twofa;
use Illuminate\Database\QueryException;
use App\User;

class ExpertController extends Controller
{
    public $expert_kyc_days = 30;
    public $expert_min_volume = 10000;

    public function getInformation() {
        $user = Auth::user();
        $expert = DB::table('experts')->where('userid', $user->id)->first();
        if(!is_null($expert)) {
            unset($expert->userid);
        }
        $kyc = DB::table('kycs')->where('userid', $user->id)->where('status', 2)->where('updated_at', '<=', Carbon::now()->subDays($this->expert_kyc_days))->first();
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => [
                'is_register' => (!is_null($expert) && $expert->status == 1) ? true : false,
                'expert' => $expert,
                'kyc' => !is_null($kyc) ? true : false,
                'volume' => DB::table('orders')->where('userid', $user->id)->where('type', 'live')->whereBetween('created_at', [Carbon::now()->subDays($this->expert_kyc_days), Carbon::now()])->sum('amount')
            ]
        ]);
    }

    public function getProfitPercent($userid) {
        $user = User::find($userid);
        $last_week = [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()];
        $win_total = DB::table('orders')->where('userid', $user->id)->where('status', 1)->where('type', 'live')->whereBetween('created_at', $last_week)->sum('amount');
        $lose_total = DB::table('orders')->where('userid', $user->id)->where('status', 2)->where('type', 'live')->whereBetween('created_at', $last_week)->sum('amount');
        $total = DB::table('orders')->where('userid', $user->id)->where('type', 'live')->whereBetween('created_at', $last_week)->sum('amount');
        $profit_percent = $total == 0 ? 0 : (($win_total - $lose_total) / $total) * 100;
        return round($profit_percent, 2);
    }

    public function postRegistration(Request $request) {
        $validator = Validator::make($request->all(), [
            'min_invest' => 'required|numeric|min:0',
            'fee' => 'required|numeric|min:0',
            'display_name' => 'required|string|max:50|alpha_dash',
            'twofa_code' => 'required|string|max:6',
            'copier_limit' => 'required|numeric'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if(!$valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The two factor code is invalid.'
            ]);
        }
        $expert = DB::table('experts')->where('userid', $user->id)->first();
        if(!is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'Access denied.'
            ]);
        }

        $display_name_exist = DB::table('experts')->where('display_name', $request->display_name)->first();
        if(!is_null($display_name_exist)) {
            return response()->json([
                'status' => 422,
                'message' => 'The display name has been existed.'
            ]);
        }

        if($user->kyc_status != 2) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account has not been verified documents.'
            ]);
        }

        $volume = DB::table('orders')->where('userid', $user->id)->where('type', 'live')->sum('amount');
        if($volume < $this->expert_min_volume) {
            return response()->json([
                'status' => 422,
                'message' => 'Your trading volume is not enough'
            ]);
        }

        $balance_total = $user->wallet_balance + $user->live_balance;
        if($balance_total < $this->expert_min_balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance is not enough.'
            ]);
        }

        if($this->getProfitPercent($user->id) < $this->expert_min_profit_percent) {
            return response()->json([
                'status' => 422,
                'message' => 'Last week profit is not enough.'
            ]);
        }

        DB::table('experts')->insert([
            'expertid' => strtoupper(uniqid('E')),
            'display_name' => (string)$request->display_name,
            'userid' => $user->id,
            'fee' => (double)$request->fee,
            'min_invest' => (double)$request->min_invest,
            'copier_limit' => (int)$request->copier_limit,
            'created_at' => date(now()),
            'updated_at' => date(now())
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Thank you for signing up to become an expert. We are conducting verification of the information you have provided and will respond within the next 24 hours. Sincerely thank!'
        ]);
    }

    public function getExperts() {
        $user = Auth::user();
        $experts = DB::table('experts as e')->join('users as u', 'e.userid', '=', 'u.id')
        ->where('e.status', 1)
        // ->where('e.userid', '<>', $user->id)
        ->select('e.*', 'u.country as country')
        ->orderBy('e.id', 'asc')->paginate(10);
        $experts_copied = DB::table('copiers')->where('copier_id', $user->id)->pluck('expert_id')->toArray();
        foreach($experts as $key => $value) {
            $experts[$key] = $value;
            $experts[$key]->country_flag = url('images/flags/'.strtolower($value->country).'.png');
            if(in_array($value->id, $experts_copied)) {
                $experts[$key]->is_copied = true;
            }
            $experts[$key]->copiers = DB::table('copiers')->where('expert_id', $value->id)->where('status', 1)->count() + $value->virtual_copiers;
            $experts[$key]->profit = $this->getProfitPercent($value->userid) + $value->virtual_profit;
            unset($experts[$key]->userid);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => $experts
        ]);
    }

    public function getExpert($expertid) {
        $user = Auth::user();
        $expertid = strtoupper($expertid);
        $expert = DB::table('experts as e')
        ->join('copiers as c', 'e.id', '=', 'c.expert_id')
        ->join('users as u', 'e.userid', '=', 'u.id')
        ->where('e.status', 1)
        ->where('e.expertid', $expertid)
        ->where('c.copier_id', $user->id)
        ->select('e.*', 'u.username as username', 'u.country as country', 'c.balance as invest_balance')
        ->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.',
            ]);
        }
        $expert->country_flag = url('images/flags/'.strtolower($expert->country).'.png');
        $expert->total_investment = DB::table('copier_fundings')->where('copier_id', $user->id)->where('expert_id', $expert->id)->where('amount', '>', 0)->sum('amount');
        unset($expert->userid);
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success',
            'data' => [
                'expert' => $expert,
                'funding_histories' => DB::table('copier_fundings')->where('copier_id', $user->id)->where('expert_id', $expert->id)->orderBy('id', 'desc')->paginate(10)
            ]
        ]);
    }

    public function copying(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'expert_id' => 'required|numeric',
            'min_copy' => 'required|numeric|min:1',
            'max_copy' => 'required|numeric|min:1',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $expert_id = (int)$request->expert_id;
        $expert = DB::table('experts')->where('id', $expert_id)->where('status', 1)->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.'
            ]);
        }
        $copier_count = DB::table('copiers')->where('expert_id', $expert_id)->where('status', 1)->count();
        if($copier_count >= $expert->copier_limit) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert has been limited followers.'
            ]);
        }

        $amount = (double)$request->amount;
        if($amount < $expert->min_invest) {
            return response()->json([
                'status' => 422,
                'message' => 'The invest amount min is $'.number_format($expert->min_invest)
            ]);
        }
        
        if($amount > $user->live_balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your live balance does not enough.'
            ]);
        }
        DB::beginTransaction();
        try {
            $copier = DB::table('copiers')->where('copier_id', $user->id)->where('expert_id', $expert_id)->where('status', 1)->first();
            if(!is_null($copier)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Access denied.'
                ]);
            }
            $data = [
                'contract_id' => strtoupper(uniqid('C')),
                'expert_id' => $expert_id,
                'copier_id' => $user->id,
                'balance' => $amount,
                'status' => 1,
                'created_at' => date(now()),
                'updated_at' => date(now())
            ];
            $data['min_copy'] = (double)$request->min_copy;
            $data['max_copy'] = (double)$request->max_copy;
            // if($request->has('stoploss') && $request->stoploss > 0) {
            //     $data['stoploss'] = (double)$request->stoploss;
            // }
            DB::table('copiers')->insert($data);
            DB::table('copier_fundings')->insert([
                'copier_id' => $user->id,
                'expert_id' => $expert_id,
                'amount' => $amount,
                'created_at' => date(now()),
                'updated_at' => date(now())
            ]);
            DB::table('users')->where('id', $user->id)->decrement('live_balance', $amount);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Copying successfully.'
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Copying has an error.'
            ]);
        }
    }

    public function addfunds(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'expertid' => 'required|string|max:255',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ]);
        }
        $expertid = strtoupper($request->expertid);
        $expert = DB::table('experts as e')
        ->join('copiers as c', 'e.id', '=', 'c.expert_id')
        ->join('users as u', 'e.userid', '=', 'u.id')
        ->where('e.status', 1)
        ->where('e.expertid', $expertid)
        ->where('c.copier_id', $user->id)
        ->select('e.*', 'u.username as username', 'u.country as country')
        ->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.',
            ]);
        }

        $amount = (double)$request->amount;
        if($amount < $expert->min_invest) {
            return response()->json([
                'status' => 422,
                'message' => 'The invest amount min is $'.number_format($expert->min_invest)
            ]);
        }
        
        if($amount > $user->live_balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your live balance does not enough.'
            ]);
        }

        DB::beginTransaction();
        try {
            DB::table('copier_fundings')->insert([
                'copier_id' => $user->id,
                'expert_id' => $expert->id,
                'amount' => $amount,
                'created_at' => date(now()),
                'updated_at' => date(now())
            ]);
            DB::table('copiers')->where('expert_id', $expert->id)->where('copier_id', $user->id)
            // ->where('status', 1)
            ->increment('balance', $amount);
            DB::table('users')->where('id', $user->id)->decrement('live_balance', $amount);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Add fund to expert is success.'
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Add funds has an error.'
            ]);
        }
    }

    public function stopCopy(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'expertid' => 'required|string'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $expertid = strtoupper($request->expertid);
        $amount = (double)$request->amount;
        $user = Auth::user();
        $expert = DB::table('experts as e')->join('copiers as c', 'e.id', '=', 'c.expert_id')
        ->where('e.expertid', $expertid)->where('c.copier_id', $user->id)->select('e.*', 'c.balance', 'c.id as copy_id', 'c.created_at as copy_date')->first();
        if(is_null($expert)) {
            return response()->json([
                'status' => 422,
                'message' => 'The expert does not exist.'
            ]);
        }
        if($amount > $expert->balance) {
            return response()->json([
                'status' => 422,
                'message' => 'Your invest balance is not enough.'
            ]);
        }
        $copy_date = Carbon::create($expert->copy_date); // Ngày bắt đầu vào gói
        $now = Carbon::now();
        $stop_fee = $now->diffInDays($copy_date) >= 30 ? 1 : 3; // Percent fee
        $total = $amount - ($amount * $stop_fee / 100);
        DB::beginTransaction();
        try {
            if(($expert->balance - $amount) >= 1) {
                DB::table('copier_fundings')->insert([
                    'copier_id' => $user->id,
                    'expert_id' => $expert->id,
                    'amount' => '-'.$amount,
                    'created_at' => date(now()),
                    'updated_at' => date(now())
                ]);
                DB::table('copiers')->where('id', $expert->copy_id)->decrement('balance', $amount);
            } else {
                DB::table('copiers')->where('id', $expert->copy_id)->delete();
                DB::table('copier_fundings')->where('copier_id', $user->id)->where('expert_id', $expert->id)->delete();
            }
            DB::table('users')->where('id', $user->id)->increment('live_balance', $total);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Copying has been stopped.'
            ]);
        } catch(QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => 'Stop copy has an error.'
            ]);
        }
    }
}
