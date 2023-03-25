<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReupLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'franchise:reup {start_date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
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
    }

    public function resetLevel($dateCal, $startDate, $endDate)
    {
        $list_active = [];
        $current = Carbon::parse($dateCal);
        // Check last week have franchise
        $lastUsersActive = DB::table('commissions')->where('commission_type','trade')->whereBetWeen('created_at',[$startDate,$endDate])->groupBy('userid')->get();
        $listUser = '';
        foreach ($lastUsersActive as $key => $item) {
            if($key == 0){
                $listUser .= '(';
                $listUser .= ' '.$item->userid;
            } else if($key == count($lastUsersActive) - 1){
                $listUser .= ', '.$item->userid;
                $listUser .= ')';
            } else{
                $listUser .= ', '.$item->userid;
                $listUser .= ' ';
            }
        }

        // Check volume last week
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
                                            WHERE u.sponsor_id <> 0 ".($listUser != ''? "AND s.id IN ".$listUser:"")."
                                            GROUP BY u.sponsor_id
                                            HAVING SUM(IFNULL(o.total, 0)) >= 2000;"));
        if (count($userActive) == 0) {
            return $list_active;
        }
        
        $users = [];
        foreach ($userActive as $item) {
            $users[] = $item->sponsor_id;
        }

        // Reup level user
        $nextWeek = $current->endOfWeek(Carbon::SUNDAY)->addWeek();
        DB::table('users')->whereIn('id', $users)->update([
            'last_week_level' => 1,
            'last_date_week' => $nextWeek,
            'last_week' => $nextWeek->weekOfYear,
            'is_franchise' => 1
        ]);

        $users = DB::table('users')->where('is_franchise', 1)->get();
        $list_active = $users->map(function ($item, $key) {
            return [$item->id, $item->username, 1];
        });
        return $list_active->toArray();
    }
}
