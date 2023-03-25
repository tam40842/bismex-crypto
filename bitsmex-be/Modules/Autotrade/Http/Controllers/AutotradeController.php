<?php

namespace Modules\Autotrade\Http\Controllers;

use App\Http\Controllers\Vuta\Vuta;
use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutotradeController extends Controller
{
    use Vuta;

    public function overview(Request $request)
    {
        $user = Auth::user();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $dayStart = Carbon::now()->subDay()->startOfDay();
        $dayEnd = Carbon::now()->subDay()->endOfDay();
        $data['package'] = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->first();
        $data['profit'] = DB::table('commissions')
            ->where('status', '=', 1)
            ->where('created_at', '>=', $dayStart)
            ->where('created_at', '<=', $dayEnd)
            ->where('userid',  Auth::id())
            ->where('commission_type', 'autotrade_com')
            ->sum('amount');

        $data['commission'] = [];
        $data['commission']['month'] = DB::table('commissions')
            ->where('status', '=', 1)
            ->where('created_at', '>=', $monthStart)
            ->where('created_at', '<=', $monthEnd)
            ->where('userid',  Auth::id())
            ->where('commission_type', 'autotrade_bonus')
            ->sum('amount');

        $active = DB::table('users')->leftJoin('autotrade_package', 'users.id', '=', 'autotrade_package.userid')
            ->where('users.sponsor_id', '=', $user->id)
            ->select(DB::raw('SUM(IF(autotrade_package.status = 1, 1,0)) as user_active, SUM(IF(autotrade_package.status is null, 1,0)) as not_active'))->get();
        $data['commission']['active'] = $active[0]->user_active;
        $data['commission']['not_active'] = $active[0]->not_active;
        $data['link_ref'] = Auth::user()->ref_id;
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    protected $packageFee = 1000;
    public function buyPackage(Request $request)
    {
        $package = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->first();
        if (!is_null($package)) {
            return response()->json([
                'status' => 422,
                'message' => 'You are already have an Package.'
            ]);
        }

        DB::beginTransaction();
        try {

            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ]);
            }

            if ($user->autotrade_balance < $this->packageFee) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your Autotrade balance does not enough.'
                ]);
            }

            TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_PACKAGE', $this->packageFee * -1, 'autotrade_balance');
            DB::table('users')->where('id', $user->id)->where('status', 1)->lockForUpdate()->decrement('autotrade_balance', $this->packageFee);
            $package_id = strtoupper(uniqid('A'));
            // Bonus 1% of package price for Sponsor user
            if ($user->sponsor_id != 0) {
                TransactionHistory::historyLiveBalance($user->sponsor_id, 'AUTOTRADE_BONUS', $this->packageFee * 0.01, 'autotrade_balance');
                DB::table('users')->where('id', $user->sponsor_id)->where('status', 1)->lockForUpdate()->increment('autotrade_balance', $this->packageFee * 0.01);

                $newData = [
                    'name' => 'Autotrade Bonus',
                    'autotrade_id' => $package_id,
                    'userid' => $user->sponsor_id,
                    'amount' => $this->packageFee * 0.01,
                    'message' => 'Autotrade bonus ' . $package_id,
                    'commission_type' => 'autotrade_bonus',
                    'status' => 1,
                ];
                DB::table('commissions')->insert($newData);
            }

            DB::table('autotrade_package')->insert([
                'package_id' => $package_id,
                'userid' => Auth::id(),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->endOfMonth(),
                'status' => 1
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Buy package success.'
            ]);
        } catch (QueryException $ex) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Have an Error.'
            ]);
        }
    }

    public function swap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
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

        $from_balance = 'autotrade_balance';
        $to_balance = 'primary_balance';
        $amount = $request->amount;
        if ($amount > $user->{$from_balance}) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance is not enough.'
            ]);
        }
        DB::beginTransaction();
        try {
            // Save transaction history
            TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_SWAP', $amount * -1, $from_balance);
            TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_SWAP', $amount, $to_balance);

            DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement($from_balance, $amount);
            DB::table('users')->where('id', $user->id)->lockForUpdate()->increment($to_balance, $amount);
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Swap successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Swap has error',
            ]);
        }
    }

    public function borrowMoney()
    {
        $package = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->first();
        if (is_null($package)) {
            return response()->json([
                'status' => 422,
                'message' => "You don't have a Package."
            ]);
        }

        DB::beginTransaction();
        try {

            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ]);
            }

            if ($package->borrow_amount > 0) {
                return response()->json([
                    'status' => 422,
                    'message' => "You have a borrow."
                ]);
            }

            DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->update([
                'borrow_amount' => $this->packageFee * 0.3,
                'borrow_date' => Carbon::now(),
                'borrow_overtime' => Carbon::now()->endOfDay(),
            ]);
            TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_BORROW', $this->packageFee * 0.3, 'autotrade_balance');
            DB::table('users')->where('id', Auth::id())->where('status', 1)->increment('autotrade_balance', $this->packageFee * 0.3);


            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Borrow success.'
            ]);
        } catch (QueryException $ex) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Have an Error.'
            ]);
        }
    }

    public function activeBot()
    {
        DB::beginTransaction();
        try {
            $package = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($package)) {
                return response()->json([
                    'status' => 422,
                    'message' => "You don't have a Package."
                ]);
            }

            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ]);
            }

            if ($package->borrow_amount > 0) {
                return response()->json([
                    'status' => 422,
                    'message' => "You have a borrow."
                ]);
            }

            $timeNow = (int) Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')->format('H');
            if ($timeNow < 7 || $timeNow > 12) {
                return response()->json([
                    'status' => 422,
                    'message' => "Time for active bot is expired.(7AM - 12AM)"
                ]);
            }

            if (!is_null($package->active_bot) && Carbon::parse($package->active_bot)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                return response()->json([
                    'status' => 422,
                    'message' => "Today, You activated bot."
                ]);
            }
            $settings = $this->get_settings(['autotrade_percent']);
            $autotradePercent = 5 / 100;
            if (isset($settings['autotrade_percent'])) {
                $autotradePercent = $settings['autotrade_percent'] / 100;
            }
            $received  = $package->received;
            $LeftReceive = $this->packageFee * $autotradePercent - $received;
            if ($LeftReceive < 2) {
                $getReceive = $LeftReceive;
            } else {
                $getReceive = rand(2, 4) / 100 * $LeftReceive;
            }

            DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->update([
                'active_bot' => Carbon::now(),
                'active_counter' => $package->active_counter + 1,
            ]);

            $newData = [
                'name' => 'Autotrade Receive',
                'autotrade_id' => $package->package_id,
                'userid' => $user->id,
                'amount' => $getReceive,
                'message' => 'Autotrade receive ' . $package->package_id,
                'commission_type' => 'autotrade_com',
                'status' => 2,
            ];
            DB::table('commissions')->insert($newData);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Active bot success.'
            ]);
        } catch (QueryException $ex) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Have an Error.'
            ]);
        }
    }

    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
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

        $package = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->first();
        if (is_null($package)) {
            return response()->json([
                'status' => 422,
                'message' => "You don't have a Package."
            ]);
        }

        $from_balance = 'autotrade_balance';
        $amount = $request->amount;
        if ($amount > $user->{$from_balance}) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance is not enough.'
            ]);
        }

        DB::beginTransaction();
        try {

            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ]);
            }

            if ($package->borrow_amount == 0) {
                return response()->json([
                    'status' => 422,
                    'message' => "You don't have a borrow."
                ]);
            }

            if ($package->borrow_amount > $amount) {
                DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->decrement('borrow_amount', $amount);
                TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_PAY', $amount * -1, 'autotrade_balance');
                DB::table('users')->where('id', Auth::id())->where('status', 1)->decrement('autotrade_balance', $amount);
            } else {
                DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->update([
                    'borrow_amount' => 0,
                    'borrow_date' => null,
                    'borrow_overtime' => null
                ]);
                TransactionHistory::historyLiveBalance($user->id, 'AUTOTRADE_PAY', $package->borrow_amount * -1, 'autotrade_balance');
                DB::table('users')->where('id', Auth::id())->where('status', 1)->decrement('autotrade_balance', $package->borrow_amount);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Borrow success.'
            ]);
        } catch (QueryException $ex) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Have an Error.'
            ]);
        }
    }

    public function getHistoryPackage(Request $request)
    {
        $user = Auth::user();
        $histories = DB::table('commissions as c')
            ->where('c.status', 1)
            ->where('c.userid', $user->id)
            ->where('c.commission_type', 'autotrade_com')
            ->leftJoin('autotrade_package as a', 'a.package_id', '=', 'c.autotrade_id')
            ->when($request->start_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '<', $request->end_date);
            })
            ->select('c.*', 'a.status as package_status', 'a.package_id as package_id')
            ->orderBy('c.created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $histories,
        ]);
    }

    public function postWithdrawCom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
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

        $package = DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->first();
        if (is_null($package)) {
            return response()->json([
                'status' => 422,
                'message' => "You don't have a Package."
            ]);
        }

        $amount = $request->amount;
        if ($amount + $package->withdraw_complete > $package->received) {
            return response()->json([
                'status' => 422,
                'message' => 'Your balance is not enough.'
            ]);
        }

        DB::beginTransaction();
        try {

            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ]);
            }

            DB::table('autotrade_package')->where('userid', Auth::id())->where('status', 1)->update([
                'withdraw_amount' => $amount,
                'withdraw_date' => Carbon::now(),
                'withdraw_status' => 1
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Borrow success.'
            ]);
        } catch (QueryException $ex) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Have an Error.'
            ]);
        }
    }
}
