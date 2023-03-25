<?php

namespace Modules\Commission\Http\Controllers;

use App\LevelCal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Commission\Entities\Level;
use Auth;
use Carbon\Carbon;
use Validator;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    use LevelCal;
    private $commission_type = ['Trade', 'Agency'];
    private $F1_volume = 500;

    public function listChild()
    {
        $user = Auth::user();
        $get_list_children = DB::select(DB::raw("SELECT 
            id,
            username,
            volume,
            sponsor_id,
            REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
        FROM
            (SELECT 
                *
            FROM
                users) AS u,
            (SELECT @pv:=" . $user->id . ", @n:=0, @visit:='" . $user->id . ":0') initialisation
        WHERE
            FIND_IN_SET(sponsor_id, @pv)
                AND LENGTH(@pv:=CONCAT(@pv, ',', id))
                AND LENGTH(@tem:=@visit)
                AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
                        INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
                        LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))
        ORDER BY level asc"));
        $list_children = [];
        foreach ($get_list_children as $key => $value) {
            $percent = isset($this->level_commission[$value->level - 1]) ? $this->level_commission[$value->level - 1] : 0;
            $list_children[$key] = $value;
            $list_children[$key]->earn = $value->volume * $percent / 100;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Get children is success.',
            'data' => $list_children
        ]);
    }

    public function searcharray($search_text, $field, $data)
    {
        foreach ($data as $key => $value) {
            if ($value->{$field} == $search_text) {
                return $value;
            }
        }
        return null;
    }

    public function searchByUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $get_list_children = DB::select(DB::raw("select id, username, volume, sponsor_id from (select * from users order by sponsor_id, id) users, (select @pv := " . $user->id . ") initialisation where find_in_set(sponsor_id, @pv) and length(@pv := concat(@pv, ',', id))"));
        $list_children = [];
        $level = [];
        $n = 1;
        foreach ($get_list_children as $key => $value) {
            if ($n > count($this->level_commission)) {
                break;
            }
            $list_children[$key] = $value;
            $level[$value->sponsor_id] = $n;
            $list_children[$key]->level = $level[$value->sponsor_id];
            $list_children[$key]->earn = $value->volume * $this->level_commission[$n - 1] / 100;
            if (!array_key_exists($value->sponsor_id, $level)) {
                $n++;
            }
        }
        $result = $this->searcharray($request->username, 'username', $list_children);
        return response()->json([
            'status' => 200,
            'message' => 'Get child is success',
            'data' => $result
        ]);
    }

    public function overview()
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }

        $commission_trade = DB::table('commissions')->where('userid', $user->id)->where('commission_type', 'trade')->select(DB::raw("
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end),0) as today,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(1)->format('Y-m-d') . "' then amount else 0 end),0) as subday,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(2)->format('Y-m-d') . "' then amount else 0 end),0) as two_day,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as month,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as submonth,
                IFNULL(SUM(amount),0) as total"))->first();
        $commission_bonus = DB::table('commissions')->where('userid', $user->id)->where('commission_type', 'trade_bonus')->select(DB::raw("
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end),0) as today,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(1)->format('Y-m-d') . "' then amount else 0 end),0) as subday,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(2)->format('Y-m-d') . "' then amount else 0 end),0) as two_day,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as month,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as submonth,
                IFNULL(SUM(amount),0) as total"))->first();

        return response()->json([
            'status' => 200,
            'data' => [
                'total_commission' => $commission_trade->total,
                'total_bonus' => $commission_bonus->total,
                'total_today' => $commission_trade->today,
                'total_bonus_today' => $commission_bonus->today,
                'total_subday' => $commission_trade->subday,
                'total_bonus_subday' => $commission_bonus->subday,
                'total_two_day' => $commission_trade->two_day,
                'total_bonus_two_day' => $commission_bonus->two_day,
                'total_month' =>  $commission_trade->month,
                'total_bonus_month' => $commission_bonus->month,
                'total_submonth' => $commission_trade->submonth,
                'total_bonus_submonth' => $commission_bonus->submonth,
            ],
        ]);
    }

    public function get_sponsorTreeV1($email)
    {
        $user = DB::table('users')->where('email', $email)->where('status', 1)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }

        $month = Carbon::now()->startOfMonth();
        $data_f1 = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')
            ->where('users.sponsor_id', $user->id)->where('orders.type', 'live')
            ->where('users.admin_setup', 0)->where('orders.created_at', '>=', $month)
            ->groupBy('date')
            ->orderBy('orders.created_at', 'ASC')
            ->get(array(
                DB::raw('Date(orders.created_at) as date'),
                DB::raw('SUM(orders.amount) as "amount"')
            ));
        $data_f1_date  = $data_f1->pluck('date')->toArray();
        $data_f1_volume  = $data_f1->pluck('amount')->toArray();
        $total_volume = $data_f1->sum('amount');

        $statistics = DB::table('users')->where('sponsor_id', $user->id)->where('admin_setup', 0)->select('id', 'email', 'username', 'status as user_status', 'created_at as registration', 'level', 'last_week_level')->get();
        if (count($statistics) > 0) {
            foreach ($statistics as $key => $value) {
                $volume = DB::table('orders')->where('userid', $value->id)->where('type', 'live')->select(DB::raw("
                SUM(case when DATE_FORMAT(created_at, '%Y-%m-%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end) as today,
                SUM(case when DATE_FORMAT(created_at, '%Y-%m-%d') = '" . Carbon::now()->subDay()->format('Y-m-d') . "' then amount else 0 end) as subday"))->first();

                $commission = DB::table('commissions')->where('userid', $value->id)->select(DB::raw("
                SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end) as today,
                SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(1)->format('Y-m-d') . "' then amount else 0 end) as subday,
                SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end) as month,
                SUM(amount) as total"))->first();

                $user_f1 = DB::table('users')->where('sponsor_id', $value->id)->where('admin_setup', 0)->count('id');
                $user_f1_active = DB::table('users')->where('sponsor_id', $value->id)->where(function ($query) {
                    $query->where('level', '=', 1)
                        ->orWhere('last_week_level', '=', 1);
                })->count('id');

                $statistics[$key]->volume_today = $volume->today;
                $statistics[$key]->volume_subday = $volume->subday;
                $statistics[$key]->commission_today = $commission->today;
                $statistics[$key]->commission_subday = $commission->subday;
                $statistics[$key]->commission_month = $commission->month;
                $statistics[$key]->commission_total = $commission->total;
                $statistics[$key]->active = $value->last_week_level > $value->level ? $value->last_week_level : $value->level;
                $statistics[$key]->total_f1 = $user_f1;
                $statistics[$key]->total_user_f1_active = $user_f1_active;
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'statistics' => $statistics,
                    'data_f1_date' => $data_f1_date,
                    'data_f1_volume' => $data_f1_volume,
                    'total_volume' => $total_volume,
                ],
            ]);
        } else {
            return response()->json([
                'status' => 422,
                'message' => 'Member has no downline',
            ]);
        }
    }

    public function get_sponsorTree($email)
    {
        $user = DB::table('users')->where('email', $email)->where('status', 1)->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }

        $month = Carbon::now()->startOfMonth();
        $data_f1 = DB::table('orders')->join('users', 'orders.userid', '=', 'users.id')
            ->where('users.sponsor_id', $user->id)->where('orders.type', 'live')
            ->where('users.admin_setup', 0)->where('orders.created_at', '>=', $month)
            ->groupBy('date')
            ->orderBy('orders.created_at', 'ASC')
            ->get(array(
                DB::raw('Date(orders.created_at) as date'),
                DB::raw('SUM(orders.amount) as "amount"')
            ));
        $data_f1_date  = $data_f1->pluck('date')->toArray();
        $data_f1_volume  = $data_f1->pluck('amount')->toArray();
        $total_volume = $data_f1->sum('amount');

        $statistics = DB::table('users')->where('sponsor_id', $user->id)->where('admin_setup', 0)->select('id', 'email', 'username', 'status as user_status', 'created_at as registration', 'level', 'last_week_level','is_franchise')->get();
        if (count($statistics) > 0) {
            foreach ($statistics as $key => $value) {
                $userData = Cache::get($value->email);
                if (!is_null($userData)) {
                    $statistics[$key]->volume_today = $userData->vol_today;
                    $statistics[$key]->volume_subday = $userData->vol_subday;
                    $statistics[$key]->commission_today = $userData->com_today;
                    $statistics[$key]->commission_subday = $userData->com_subday;
                    $statistics[$key]->commission_month = $userData->com_month;
                    $statistics[$key]->commission_total = $userData->com_total;
                    $statistics[$key]->total_f1 = $userData->f1;
                    $statistics[$key]->total_user_f1_active = $userData->f1_active;
                    $statistics[$key]->active = $value->is_franchise;
                }
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'statistics' => $statistics,
                    'data_f1_date' => $data_f1_date,
                    'data_f1_volume' => $data_f1_volume,
                    'total_volume' => $total_volume,
                ],
            ]);
        } else {
            return response()->json([
                'status' => 422,
                'message' => 'Member has no downline',
            ]);
        }
    }

    public function getVolumeByLevelToday($userid, $level)
    {
        if ($level > 0) {
            $f1_volume = DB::select(DB::raw("select IFNULL(sum(total), 0) as f1_volume from 
            (SELECT 
                id,
                username,
                volume,
                sponsor_id,
                REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
            FROM
                (SELECT 
                    *
                FROM
                    users) AS u,
                (SELECT @pv:=" . $userid . ", @n:=0, @visit:='" . $userid . ":0') initialisation
            WHERE
                FIND_IN_SET(sponsor_id, @pv)
                    AND LENGTH(@pv:=CONCAT(@pv, ',', id))
                    AND LENGTH(@tem:=@visit)
                    AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
                            INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
                            LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))) as u left join 
            (select sum(amount) as total, id, userid from orders where type = 'live' AND 
            (created_at BETWEEN '" . Carbon::now()->startOfWeek() . "' AND '" . Carbon::now()->endOfWeek() . "') 
            group by userid) as o ON u.id = o.userid where total > 0 and u.level = " . $level));
            return isset($f1_volume[0]) ? $f1_volume[0]->f1_volume : 0;
        }
        return 0;
    }

    public function getChart()
    {
        $user = Auth::user();
        $get_list_children = DB::select(DB::raw("select id from (select * from users order by sponsor_id, id) users, (select @pv := " . $user->id . ") initialisation where find_in_set(sponsor_id, @pv) and length(@pv := concat(@pv, ',', id))"));
        $list_children = [];
        foreach ($get_list_children as $key => $value) {
            $list_children[] = $value->id;
        }
        $_referrals = DB::select(DB::raw('select DATE_FORMAT(created_at, "%Y-%m-%d") as day, count(*) as total from users where id IN (' . implode(',', $list_children) . ') and DATE_FORMAT(created_at, "%Y-%m-%d") IS NOT NULL group by day'));
        $chart_referrals = [];
        foreach ($_referrals as $key => $value) {
            $chart_referrals[$value->day] = $value->total;
        }
        $_agencies = DB::select(DB::raw('select DATE_FORMAT(join_agency_at, "%Y-%m-%d") as day, count(*) as total from users where id IN (' . implode(',', $list_children) . ') and DATE_FORMAT(join_agency_at, "%Y-%m-%d") IS NOT NULL group by day'));
        $chart_agencies = [];
        foreach ($_agencies as $key => $value) {
            $chart_agencies[$value->day] = $value->total;
        }
        $_commission = DB::select(DB::raw('select DATE_FORMAT(created_at, "%Y-%m-%d") as day, sum(amount) as total from commissions where userid = ' . $user->id . ' group by day'));
        $chart_commission = [];
        foreach ($_commission as $key => $value) {
            $chart_commission[$value->day] = $value->total;
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get chart data is success',
            'data' => [
                '_referrals' => ['name' => 'Total Referrals', 'data' => $chart_referrals],
                '_agencies' => ['name' => 'Total Agencies', 'data' => $chart_agencies],
                '_commission' => ['name' => 'Total Commission', 'data' => $chart_commission]
            ]
        ]);
    }

    public function histories(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }
        $histories = DB::table('commissions')->join('users', 'commissions.f_userid', '=', 'users.id')->where('commissions.userid', $user->id);

        if (isset($request->date_from) && isset($request->date_to)) {
            $date_start = Carbon::create($request->date_from);
            $date_end = !is_null($request->date_to) ? Carbon::create($request->date_to) : Carbon::now();
            $histories = $histories->whereDate('commissions.created_at', '>=', $date_start)->whereDate('commissions.created_at', '<=', $date_end);
        }

        $histories = $histories->select('commissions.*', 'users.username')->orderBy('commissions.created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Get data success',
            'data' => $histories
        ]);
    }


    public function getHistoriesFilter(Request $request)
    {
        $user = Auth::user();
        $today = now();
        $histories = DB::table('commissions')->where('userid', $user->id)->select('id', 'level', 'commission_type', DB::raw('count(id) as traders_count'), DB::raw('sum(volume) as total_volume'), 'created_at', DB::raw('sum(amount) as earn'))->groupBy('level')->groupBy(DB::raw('date(created_at)'));
        if ($request->type && in_array($request->type, $this->commission_type)) {
            if ($request->type == 'Trade') {
                $histories = $histories->where('commission_type', $request->type)->where('created_at', '<', $today);
            }
            $histories = $histories->where('commission_type', $request->type);
        }
        $time_range = [
            'Week' => Carbon::now()->startOfWeek(),
            'Month' => Carbon::now()->startOfMonth(),
            'Year' => Carbon::now()->startOfYear(),
            'Today' => Carbon::now()->startOfDay()
        ];
        $tomorrow = Carbon::tomorrow()->startOfDay();
        if ($request->time && array_key_exists($request->time, $time_range) && !$request->date) {
            $histories = $histories->whereBetween('created_at', [$time_range[$request->time], $tomorrow]);
        }
        if ($request->date && !$request->time) {
            $date_start = Carbon::create($request->date['start']);
            $date_end = !is_null($request->date['end']) ? Carbon::create($request->date['end']) : $today;
            $histories = $histories->whereDate('created_at', '>=', $date_start)->whereDate('created_at', '<=', $date_end);
        }
        $histories = $histories->orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'status' => 200,
            'message' => 'Get data success',
            'data' => $histories
        ]);
    }

    // public function getUserbyLevel(Request $request) {
    //     if($request->level == 'all') {
    //         $user = Auth::user();
    //         $get_list_children = DB::select(DB::raw("SELECT 
    //             id,
    //             username,
    //             volume,
    //             sponsor_id,
    //             REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
    //         FROM
    //             (SELECT 
    //                 *
    //             FROM
    //                 users) AS u,
    //             (SELECT @pv:=".$user->id.", @n:=0, @visit:='".$user->id.":0') initialisation
    //         WHERE
    //             FIND_IN_SET(sponsor_id, @pv)
    //                 AND LENGTH(@pv:=CONCAT(@pv, ',', id))
    //                 AND LENGTH(@tem:=@visit)
    //                 AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
    //                         INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
    //                         LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))
    //         ORDER BY level asc"));
    //         $list_children = [];
    //         foreach($get_list_children as $key => $value) {
    //             $percent = isset($this->level_commission[$value->level - 1]) ? $this->level_commission[$value->level - 1] : 0;
    //             $list_children[$key] = $value;
    //             $list_children[$key]->earn = $value->volume * $percent / 100;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Get children is success.',
    //             'data' => $list_children
    //         ]);
    //     }
    //     $validator = Validator::make($request->all(), ['level' => 'required|numeric']);
    //     if($validator->fails()) {
    //         return response()->json([[
    //             'status' => 422,
    //             'message' => $validator->errors()->first()
    //         ]]);
    //     }
    //     if($request->level <= 0) {
    //         return response()->json([[
    //             'status' => 422,
    //             'message' => 'The level number invalid.'
    //         ]]);
    //     }
    //     $level = (int)$request->level;
    //     $user = Auth::user();
    // 	$get_list_children = DB::select(DB::raw("SELECT * from (SELECT 
    //         id,
    //         username,
    //         volume,
    //         sponsor_id,
    //         REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
    //     FROM
    //         (SELECT 
    //             *
    //         FROM
    //             users) AS u,
    //         (SELECT @pv:=".$user->id.", @n:=0, @visit:='".$user->id.":0') initialisation
    //     WHERE
    //         FIND_IN_SET(sponsor_id, @pv)
    //             AND LENGTH(@pv:=CONCAT(@pv, ',', id))
    //             AND LENGTH(@tem:=@visit)
    //             AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
    //                     INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
    //                     LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))
    //     ORDER BY level asc) as u WHERE u.level = ".$level));
    //     $list_children = [];
    // 	foreach($get_list_children as $key => $value) {
    //         $percent = isset($this->level_commission[$value->level - 1]) ? $this->level_commission[$value->level - 1] : 0;
    //         $list_children[$key] = $value;
    //         $list_children[$key]->earn = $value->volume * $percent / 100;
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Get children is success.',
    //         'data' => $list_children
    //     ]);
    // }

    // public function getPersonalLevel() {
    //     $user = Auth::user();
    //     $level = DB::select('SELECT level FROM bitse.users where id = "'.$user->id.'";');
    //     $personal_volume = DB::select('SELECT sum(amount) as personal_volume , created_at FROM bitse.orders where userid = "'.$user->id.'" and created_at >date_sub(curdate(), interval 1 week) order by created_at desc;');
    //     $volume_branch = DB::select('SELECT sum(amount) as volume_branch FROM bitse.commissions where  userid = "'.$user->id.'" and created_at >date_sub(curdate(), interval 1 week) order by created_at desc;');
    //     $num_f1_active = DB::select('SELECT count(id) as num_f1_active FROM bitse.commissions where  userid = "'.$user->id.'" and created_at >date_sub(curdate(), interval 1 week) order by created_at desc;');
    //     $data = new \stdClass();
    //     $data->level = $level;
    //     $data->personal_volume = $personal_volume;
    //     $data->volume_branch = $volume_branch;
    //     $data->num_f1_active =$num_f1_active;
    //     return response()->json([
    //         'message' => 'Get data success',
    //         'data' => $data
    //         ],200);
    // }

    public function getLevels()
    {
        $user = Auth::user();

        $startWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
        $startMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        $user_active = 0;

        $level = DB::table('commissions')->where('userid', $user->id)->orderby('id', 'desc')->first();
        $personal_volume = DB::table('orders')->where('type', 'live')->where('userid', $user->id)->whereBetween('created_at', [$startWeek, $endOfWeek])->sum('amount');
        $usersF1 = DB::table('users')->where('sponsor_id', $user->id)->pluck('id');
        if (!is_null($usersF1)) {
            $volume_week_F1 = DB::table('orders')->whereIn('userid', $usersF1)->where('type', 'live')->whereBetween('created_at', [$startWeek, $endOfWeek])
                ->select('orders.*', DB::raw('SUM(amount) as "volume"'))->get();

            foreach ($volume_week_F1 as $key => $value) {
                if ($value->volume >= $this->F1_volume) {
                    $user_active++;
                }
            }
        }

        $total_branch_volume = DB::table('users')->join('orders', 'users.id', '=', 'orders.userid')
            ->where('users.sponsor_code', 'LIKE', '%' . $user->sponsor_ref . '%')
            ->where('orders.type', 'live')->whereBetween('orders.created_at', [$startWeek, $endOfWeek])
            ->sum('orders.amount');

        $commission_month = DB::table('commissions')->where('userid', $user->id)->where('commission_type', 'trade')->where('status', 1)->where('created_at', '>=', $startMonth)->sum('amount');

        $levels = DB::table('levels')->where('status', 1)->orderBy('level', 'asc')->get();
        foreach ($levels as $key => $value) {
            $user_branch = DB::table('users')->where('sponsor_code', 'LIKE', '%' . $user->sponsor_ref . '%')->where('sponsor_level', $value->level)->pluck('id');
            $levels[$key]->count_branch = count($user_branch);
            $levels[$key]->branch_volume = DB::table('orders')->whereIn('userid', $user_branch)->where('type', 'live')->whereBetween('created_at', [$startWeek, $endOfWeek])->sum('amount');
            $levels[$key]->commission_bonus = DB::table('commissions')->where('commission_type', 'trade')->where('level', $value->level)->whereBetween('created_at', [$startWeek, $endOfWeek])->select('amount')->first();
        }

        return response()->json([
            'status' => 200,
            'message' => 'Get data success',
            'data' => [
                'level' => !is_null($level) ? $level : 0,
                'levels' => $levels,
                'personal_volume' => $personal_volume,
                'user_active' => $user_active,
                'total_branch_volume' => $total_branch_volume,
                'commission_month' => !is_null($commission_month) ? $commission_month : 0,
            ]
        ]);
    }

    public function bonus_F0($sponsor_id, $F0_volume_week, $check_F1_active, $n = 1)
    {
        /**
         * các quy ước trong bản table (không được stop ngang các level, phải stop theo thứ tự)
         * kiểm tra user F0 là users F1 theo từng levels
         * dùng đệ quy để check từng level sắp xếp từ level nhỏ đến lớn
         */
        $level = DB::table('levels')->where('status', 1)->where('level', $n)->first();
        if (!is_null($level)) {
            if ($check_F1_active > 0 && $check_F1_active >= $level->f1_count && $F0_volume_week >= $level->volume_personal) {
                $user = DB::table('users')->where('id', $sponsor_id)->first();
                $startWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
                $week = Carbon::now()->subDay(7)->format("YW");

                if (!is_null($user)) {
                    $user_branch = DB::table('users')->where('sponsor_code', 'LIKE', '%' . $user->sponsor_ref . '%')->where('sponsor_level', $level->level)->pluck('id');
                    if (!is_null($user_branch)) {
                        $total_volume_level = DB::table('orders')->whereIn('userid', $user_branch)->where('type', 'live')->whereBetween('created_at', [$startWeek, $endOfWeek])->sum('amount');
                        $week_data = DB::table('commissions')->where('userid', $user->id)->where('level', $level->level)->where('commission_type', 'trade')->whereBetween('created_at', [$startWeek, $endOfWeek])->first();
                        $bonus_amount = $total_volume_level * $level->percent / 100;
                        if (is_null($week_data)) {
                            // DB::table('users')->where('id', $user->id)->increment('bonus_balance', $bonus_amount);
                            DB::table('commissions')->insert([
                                'name' => 'Trade commission',
                                'userid' => $user->id,
                                'amount' => $bonus_amount,
                                'ref_id' => 0,
                                'level' => $level->level,
                                'volume' => $total_volume_level,
                                'message' => 'Trade commission',
                                'commission_type' => 'trade',
                                'status' => 0,
                                'yearweek' => Carbon::now()->format("YW"),
                                'created_at' => now()
                            ]);
                        } else {
                            // DB::table('users')->where('id', $user->id)->increment('bonus_balance', $bonus_amount - $week_data->amount);
                            $update_bonus_level = DB::table('commissions')->where('id', $week_data->id)->update([
                                'amount' => $bonus_amount,
                                'volume' => $total_volume_level,
                                'updated_at' => now()
                            ]);
                            /**
                             * kiểm tra nếu hôm nay là cuối tuần gửi email user bonus
                             */
                            if (Carbon::now()->format("Y-m-d") == $endOfWeek) {
                                SendEmail::dispatch($user->email, 'System development commissions', 'commission', ['user' => $user, 'amount' => $bonus_amount, 'volume' => $total_volume_level, 'week' => $week]);
                            }
                        }
                    }
                }
            }
            $n = $level->level + 1;
            $this->bonus_F0($sponsor_id, $F0_volume_week, $check_F1_active, $n);
        }
    }

    public function check_volume_F0($sponsor_id)
    {
        /**
         * kiểm tra user F0 thỏa thuận theo yêu cầu hệ thống
         */
        $startWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        if ($sponsor_id > 0) {
            $F0_volume_week = DB::table('users')->join('orders', 'users.id', '=', 'orders.userid')->where('users.id', $sponsor_id)->where('orders.userid', $sponsor_id)->where('orders.type', 'live')->whereBetween('orders.created_at', [$startWeek, $endOfWeek])->sum('orders.amount');
            if (!is_null($F0_volume_week)) {
                return $F0_volume_week;
            }
        }
    }

    public function check_F1_active($sponsor_id, $active = 0, $total_volume_f1 = 0)
    {
        /**
         * kiểm tra users F1 thỏa thuận theo yêu cầu hệ thống
         */
        $startWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        $users_F1 = DB::table('users')->where('sponsor_id', $sponsor_id)->pluck('id');
        $volume_week_F1 = DB::table('orders')->whereIn('userid', $users_F1)->where('type', 'live')->whereBetween('created_at', [$startWeek, $endOfWeek])
            ->select('orders.userid', DB::raw('SUM(amount) as "volume"'))->get();

        if (!is_null($volume_week_F1)) {
            foreach ($volume_week_F1 as $key => $value) {
                $total_volume_f1 += $value->volume;
                if ($value->volume >= $this->F1_volume) {
                    $active++;
                }
            }

            return [
                'active' => $active,
                'total_volume_f1' => $total_volume_f1
            ];
        }
    }

    public function getDetail($id)
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }
        $commission = DB::table('commissions')->where('id', $id)->where('userid', $user->id)->first();
        if (is_null($commission)) {
            return response()->json([
                'status' => 422,
                'message' => 'Commission not found.'
            ], 422);
        }
        $com_date = Carbon::now()->parse($commission->com_date)->format('Y-m-d');
        $orders = DB::table('orders')
            ->join('users', 'orders.userid', '=', 'users.id')
            ->where('orders.userid', $commission->f_userid)
            ->where('orders.type', 'live')
            ->whereDate('orders.created_at', $com_date)
            ->select('orders.*', 'users.username')
            ->orderBy('orders.created_at', 'desc')->get();
        $commission = DB::table('commissions')->where('f_userid', $commission->f_userid)->where('com_date', $commission->com_date)->orderBy('id', 'desc')->first();

        return response()->json([
            'status' => 200,
            'data' => [
                'detail' => $commission,
                'histories_orders' => $orders,
            ]
        ]);
    }

    public function getTotalVolumeF1(Request $request)
    {
        $user = Auth::user();
        $usersF1 = DB::table('users')->where('sponsor_id', $user->id)->pluck('id')->toArray();

        $month = Carbon::now()->startOfMonth();
        if ($request->month != '') {
            $month = Carbon::parse($request->month)->startOfMonth();
        }
        $data = [];

        if (count($usersF1) > 0) {
            foreach ($usersF1 as $key => $value) {
                $user = DB::table('users')->where('id', $value)->first();
                if ($user) {
                    $user_children = DB::table('users')->where('sponsor_code', 'LIKE', '%' . $user->sponsor_ref . '%')->pluck('id')->toArray();
                    $total_volume = DB::table('orders')->whereIn('userid', $user_children)->where('type', 'live')->whereMonth('created_at', $month)->sum('amount');
                    $data[$key]['email'] = $user->email;
                    $data[$key]['total_volume'] = $total_volume;
                }
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function overviewFranchise(Request $request)
    {
        $user = DB::table('users')->where('status', 1)->where('admin_setup', 0)->where('id', Auth::id())->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'This account has been banned or deactived.'
            ], 422);
        }

        $commission_trade = DB::table('commissions')->where('userid', $user->id)->where('commission_type', 'trade')->select(DB::raw("
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end),0) as today,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(1)->format('Y-m-d') . "' then amount else 0 end),0) as subday,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(2)->format('Y-m-d') . "' then amount else 0 end),0) as two_day,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as month,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as submonth,
                IFNULL(SUM(amount),0) as total"))->first();
        $commission_franchise = DB::table('commissions')->where('userid', $user->id)->where('commission_type', 'ib_franchise')->select(DB::raw("
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->format('Y-m-d') . "' then amount else 0 end),0) as today,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(1)->format('Y-m-d') . "' then amount else 0 end),0) as subday,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') = '" . Carbon::now()->subDays(2)->format('Y-m-d') . "' then amount else 0 end),0) as two_day,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as month,
                IFNULL(SUM(case when STR_TO_DATE(com_date,'%Y%m%d') BETWEEN '" . Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . "' and '" . Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d') . "' then amount else 0 end),0) as submonth,
                IFNULL(SUM(amount),0) as total"))->first();

        return response()->json([
            'status' => 200,
            'data' => [
                'total_commission' => $commission_trade->total,
                'total_franchise' => $commission_franchise->total,
                'com_today' => $commission_trade->today,
                'franchise_today' => $commission_franchise->today,
                'com_subday' => $commission_trade->subday,
                'franchise_subday' => $commission_franchise->subday,
                'com_two_day' => $commission_trade->two_day,
                'franchise_two_day' => $commission_franchise->two_day,
                'com_month' =>  $commission_trade->month,
                'franchise_month' => $commission_franchise->month,
                'com_submonth' => $commission_trade->submonth,
                'franchise_submonth' => $commission_franchise->submonth,
            ],
        ]);
    }

    public function getHistoryFranchise(Request $request)
    {
        $user = Auth::user();
        $histories = DB::table('commissions as c')
            ->where('c.userid', $user->id)
            ->where(function ($query) {
                return $query->orWhere('commission_type', 'trade')->orWhere('commission_type', 'ib_franchise');
            })
            ->leftJoin('users as u', 'u.id', '=', 'c.f_userid')
            ->when($request->start_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '<', $request->end_date);
            })
            ->select('c.*', 'u.username as username', 'u.email as email')
            ->orderBy('c.created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $histories,
        ]);
    }

    public function getHistoryOverviewGroupDay(Request $request)
    {
        $user = Auth::user();
        $histories = DB::table('commissions as c')
            ->where('c.status', 1)
            ->where('c.userid', $user->id)
            ->where(function ($query) {
                return $query->orWhere('commission_type', 'trade')->orWhere('commission_type', 'ib_franchise');
            })
            ->leftJoin('autotrade_package as a', 'a.package_id', '=', 'c.autotrade_id')
            ->when($request->start_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date != "null", function ($query) use ($request) {
                return $query->where('c.created_at', '<', $request->end_date);
            })
            ->groupBy('com_date')
            ->select(DB::raw("IFNULL(SUM(case when commission_type = 'trade' then amount else 0 end),0) as com,
                            IFNULL(SUM(case when commission_type = 'ib_franchise' then amount else 0 end),0) as ib_franchise, STR_TO_DATE(com_date,'%Y%m%d') as date_com"))
            ->orderBy('c.created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $histories,
        ]);
    }
}
