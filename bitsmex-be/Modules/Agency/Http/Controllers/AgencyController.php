<?php

namespace Modules\Agency\Http\Controllers;

use App\Franchise;
use App\TransactionHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Agency\Entities\Agency;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AgencyController extends Controller
{
    use Franchise;

    private $agency_fee = 40; // USD
    private $floors = [70, 10, 3, 2, 1];

    public function getActive(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account has been banned or does not active.'
            ], 200);
        }

        if ($user->is_agency) {
            return response()->json([
                'status' => 422,
                'message' => 'You are already Agency.'
            ], 200);
        }

        if ($user->live_balance < $this->agency_fee) {
            return response()->json([
                'status' => 422,
                'message' => 'Your Primary balance does not enough.'
            ], 200);
        }

        $getUser = DB::table('users')->where('id', $user->id);
        $bonus_trade = $getUser->decrement('live_balance', $this->agency_fee);
        //$bonus_token = $getUser->increment('mmb_balance', 1000);
        $getUser->update([
            'is_agency' => 1,
            'join_agency_at' => date(now())
        ]);

        // DB::table('mmb_histories')->insert([
        //     'userid' => $user->id,
        //     'type' => 'agency',
        //     'amount' => 1000,
        //     'content' => 'Buy agency license is success.',
        //     'created_at' => now()
        // ]);

        $this->uplineCommission($user->sponsor_id, $user->id);
        return response()->json([
            'status' => 200,
            'is_agency' => true,
            'message' => 'Buy agency license is success.'
        ], 200);
    }

    public function uplineCommission($sponsor_id, $userid, $floor = 1)
    {
        if ($floor <= count($this->floors)) {
            if ($sponsor_id > 0) {
                $user = DB::table('users')->where('id', $sponsor_id)->first();
                if (!is_null($user)) {
                    $bonus_accept = false;
                    if ($user->is_agency) {
                        $bonus_accept = true;
                    }
                    if ($bonus_accept) {
                        $bonus_amount = $this->agency_fee * $this->floors[$floor - 1] / 100;
                        DB::table('users')->where('id', $sponsor_id)->increment('live_balance', $bonus_amount);
                        DB::table('commissions')->insert([
                            'name' => 'Agency commission',
                            'userid' => $sponsor_id,
                            'amount' => $bonus_amount,
                            'ref_id' => $userid,
                            'level' => $floor,
                            'volume' => $this->agency_fee,
                            'message' => 'Agency commission',
                            'commission_type' => 'agency',
                            'status' => 1,
                            'yearweek' => Carbon::now()->format("YW")
                        ]);
                        $floor++;
                    }
                    $this->uplineCommission($user->sponsor_id, $userid, $floor);
                }
            }
        }
    }

    public function activeFranchise()
    {
        DB::beginTransaction();
        try {
            $user = DB::table('users')->where('id', Auth::id())->where('status', 1)->lockForUpdate()->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your account has been banned or does not active.'
                ], 200);
            }

            if ($user->is_franchise) {
                return response()->json([
                    'status' => 422,
                    'message' => 'You are already Franchise.'
                ], 200);
            }

            if ($user->primary_balance < $this->franchise_fee) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your Primary balance does not enough.'
                ], 200);
            }

            TransactionHistory::historyLiveBalance($user->id, 'ACTIVE_FRANCHISE', $this->franchise_fee * -1, 'primary_balance');
            DB::table('users')->where('id', $user->id)->where('status', 1)->lockForUpdate()->decrement('primary_balance', $this->franchise_fee);
            DB::table('users')->where('id', $user->id)->update([
                'is_franchise' => 1,
                'last_week' => Carbon::now()->weekOfYear,
                'last_date_week' => Carbon::now()->endOfWeek(),
                'last_week_level' => 1,
                'join_franchise_date' => Carbon::now()
            ]);
            $this->uplineFranchise($user->sponsor_id, $user->id);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Join Franchise is success.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'is_agency' => true,
                'message' => 'Has an error.'
            ]);
        }
    }
}
