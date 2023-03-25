<?php

namespace App\Console\Commands;

use App\Level;
use App\TransactionHistory;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalCommission extends Command
{
    use Level;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:level {start_date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make level commission for user weekly.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get time input
        $this->info('Start calculation commission level.');
        $monthDay = Carbon::now()->subDay()->format("Ymd");
        $startDate = Carbon::now()->subDay()->startOfDay();
        $endDate = Carbon::now()->subDay()->endOfDay();
        $startDate2 = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $endDate2 = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
        $dateCal = Carbon::now()->format('Y-m-d');
        if (!is_null($this->argument('start_date'))) {
            $monthDay = Carbon::parse($this->argument('start_date'))->format("Ymd");
            $startDate = Carbon::parse($this->argument('start_date'))->startOfDay();
            $endDate = Carbon::parse($this->argument('start_date'))->endOfDay();
            $startDate2 = Carbon::parse($this->argument('start_date'))->subWeek()->startOfWeek(Carbon::MONDAY);
            $endDate2 = Carbon::parse($this->argument('start_date'))->subWeek()->endOfWeek(Carbon::SUNDAY);
            $dateCal = Carbon::parse($this->argument('start_date'))->format('Y-m-d');
        }

        // Get user active yesterday
        $this->info('<]==[ LIST USER ACTIVE ]==[>');
        $headersLevel = ['Id', 'username', 'Level'];
        $resultLevel = [];
        $resultLevel = array_merge($resultLevel, $this->resetLevel($dateCal,$startDate2, $endDate2));
        $this->table($headersLevel, $resultLevel);

        // Get commmission of F1 level 0
        $headersCommissions = ['username', 'F.username', 'F.Vol', 'F.No', 'F.IB', 'Percent', 'Commission'];
        $resultCommissions = [];
        $users = DB::select(DB::raw("SELECT 
                                        u.id, u.level, u.username, u.is_franchise, u.last_week_level, u.sponsor_id, SUM(IFNULL(o.total, 0)) AS vol
                                    FROM
                                        users AS u
                                            LEFT JOIN
                                        (SELECT 
                                            IFNULL(SUM(amount), 0) AS total, orders.userid
                                        FROM
                                            orders
                                        WHERE
                                            orders.type = 'live'
                                                AND orders.created_at BETWEEN '" . $startDate . "' AND '" . $endDate . "'
                                        GROUP BY orders.userid) AS o ON o.userid = u.id
                                    WHERE u.admin_setup = 0 
                                    GROUP BY u.id 
                                    HAVING SUM(IFNULL(o.total, 0)) > 0 
                                    ORDER BY u.id DESC;"));

        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $bar->advance();
            $resultCommissions = array_merge($resultCommissions, $this->processCommission($user, $monthDay));
        }

        $bar->finish();
        $this->info('');
        $this->info('<]==[ LIST COMMISSION ]==[>');
        $this->table($headersCommissions, $resultCommissions);

        if (is_null($this->argument('start_date'))) {
            $this->info('');
            $this->info('<]==[ LIST USER ACTIVE ]==[>');
            $headersLevel = ['Id', 'username', 'Level'];
            $resultLevel = [];
            $resultLevel = array_merge($resultLevel, $this->resetLevel($dateCal,Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY), Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)));
            $this->table($headersLevel, $resultLevel);
            $this->call('commission:pay');
        }
        $this->info('The command was successful!');
    }

    /**
     * Process Commission
     */
    public function processCommission($user,  $monthDay, $calLevel = 0)
    {
        $resultCommissions = [];
        // Check level sponsor
        $sponsor = DB::table('users')->where('id', $user->sponsor_id)->first();
        if (is_null($sponsor)) {
            return $resultCommissions;
        }

        $resultCommissions = $this->insertCommission($user, $monthDay, $calLevel);

        return $resultCommissions;
    }

    public function processBonus($user,  $monthDay)
    {
        $result = [];
        $div = 2;
        $limitUnActive = 0;
        $commission = round($user->total * 0.5, 2);
        if ($commission <= 0) {
            return $result;
        }

        $uplineUser = DB::select(DB::raw("SELECT u.id, u.username, u.is_franchise, IF(u.last_week_level > u.level,u.last_week_level,u.level) AS level, u.f_no
                                        FROM
                                            (WITH RECURSIVE tree
                                            AS (
                                                SELECT id, username, is_franchise, volume, sponsor_id, level, last_week_level, 1 as f_no
                                                FROM users
                                                WHERE id = " . $user->sponsor_id . "
                                                UNION ALL
                                                SELECT i.id, i.username, i.is_franchise, i.volume, i.sponsor_id, i.level, i.last_week_level, t.f_no+1 as f_no
                                                FROM users i INNER JOIN tree t ON  i.id = t.sponsor_id
                                                )
                                            SELECT t.id, t.username, t.is_franchise, t.level, t.last_week_level, t.f_no
                                            FROM tree as t) as u 
                                            WHERE u.f_no;"));

        foreach ($uplineUser as $item) {
            if($limitUnActive == 7){
                return $result;
            }

            if ($item->is_franchise == 0) {
                $limitUnActive++;
                continue;
            } else{
                $limitUnActive = 0;
            }

            $trade_bonus = $commission;
            $paid = DB::table('commissions')
                ->where('com_date', $monthDay)
                ->where('userid', $item->id)
                ->where('commission_type', 'trade_bonus')
                ->where('f_userid', $user->id)
                ->where('f_no', $item->f_no)
                ->sum('amount');

            $hasPay = round($trade_bonus - $paid, 2);

            if ($hasPay <= 0) {
                $commission =  round($commission / 2, 2);
                $div = $div * 2;
                continue;
            }

            DB::beginTransaction();
            try {
                $newData = [
                    'name' => 'IB bonus',
                    'userid' => $user->id,
                    'amount' => $hasPay,
                    'message' => 'IB bonus',
                    'commission_type' => 'trade_bonus',
                    'status' => 0,
                    'com_date' => $monthDay
                ];
                DB::table('commissions')->insert($newData);
                DB::commit();
                $result[] = [$item->username, $user->username, $user->total, 0.5,  $hasPay];
                $commission =  round($commission / 2, 2);
                $div = $div * 2;
            } catch (QueryException $ex) {
                DB::rollBack();
                continue;
            }
        }

        return $result;
    }

    public function resetLevel($dateCal, $startDate, $endDate)
    {
        $list_active = [];
        $current = Carbon::parse($dateCal);
        if($current->dayOfWeek == 1){
            DB::table('users')->where('last_date_week','<', $current)->update([
                'last_week_level' => 0,
                'is_franchise' => 0,
                'last_reset_level' => $current,
            ]);
        }
        
        $userActive = DB::select(DB::raw("SELECT 
                                                u.sponsor_id
                                            FROM
                                                users AS u
                                                    LEFT JOIN
                                                        (SELECT 
                                                            IFNULL(SUM(amount), 0) AS total, orders.userid
                                                        FROM
                                                            orders
                                                        WHERE
                                                            orders.type = 'live'
                                                            AND orders.created_at BETWEEN '" . $startDate . "' AND '" . $endDate . "'
                                                        GROUP BY orders.userid) AS o ON o.userid = u.id
                                                    LEFT JOIN users AS s on u.sponsor_id = s.id
                                            WHERE u.sponsor_id <> 0 AND s.is_franchise = 1
                                            GROUP BY u.sponsor_id
                                            HAVING SUM(IFNULL(o.total, 0)) >= 2000;"));
        if (count($userActive) == 0) {
            return $list_active;
        }
        
        $users = [];
        foreach ($userActive as $item) {
            $users[] = $item->sponsor_id;
        }

        $nextWeek = $current->endOfWeek(Carbon::SUNDAY)->addWeek();
        DB::table('users')->whereIn('id', $users)->update([
            'last_week_level' => 1,
            'last_date_week' => $nextWeek,
            'last_week' => $nextWeek->weekOfYear,
        ]);

        $users = DB::table('users')->where('is_franchise', 1)->get();
        $list_active = $users->map(function ($item, $key) {
            return [$item->id, $item->username, 1];
        });
        return $list_active->toArray();
    }
}
