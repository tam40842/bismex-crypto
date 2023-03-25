<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommissionStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:statistics';

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
        $this->info('Start calculation commission level.');
        $now = Carbon::now();
        $users = DB::select(DB::raw("SELECT 
                                        tree.id,
                                        tree.email,
                                        tree.username,
                                        IFNULL(f.f1, 0) AS f1,
                                        IFNULL(f.f1_active, 0) AS f1_active,
                                        IFNULL(o.vol_today, 0) AS vol_today,
                                        IFNULL(o.vol_subday, 0) AS vol_subday,
                                        IFNULL(c.com_today, 0) AS com_today,
                                        IFNULL(c.com_subday, 0) AS com_subday,
                                        IFNULL(c.com_month, 0) AS com_month,
                                        IFNULL(c.com_total, 0) AS com_total
                                    FROM
                                        users AS tree
                                            LEFT JOIN
                                        (SELECT 
                                            userid,
                                                SUM(CASE
                                                    WHEN DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') THEN amount
                                                    ELSE 0
                                                END) AS vol_today,
                                                SUM(CASE
                                                    WHEN DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d') THEN amount
                                                    ELSE 0
                                                END) AS vol_subday
                                        FROM
                                            orders
                                        WHERE
                                            type = 'live'
                                        GROUP BY userid) AS o ON o.userid = tree.id
                                            LEFT JOIN
                                        (SELECT 
                                            userid,
                                                SUM(CASE
                                                    WHEN DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') THEN amount
                                                    ELSE 0
                                                END) AS com_today,
                                                SUM(CASE
                                                    WHEN DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d') THEN amount
                                                    ELSE 0
                                                END) AS com_subday,
                                                SUM(CASE
                                                    WHEN created_at BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND DATE_FORMAT(NOW(), '%Y-%m-%d') THEN amount
                                                    ELSE 0
                                                END) AS com_month,
                                                SUM(amount) AS com_total
                                        FROM
                                            commissions
                                        GROUP BY userid) AS c ON c.userid = tree.id
                                            LEFT JOIN
                                        (SELECT 
                                            l.sponsor_id,
                                                IFNULL(COUNT(l.id), 0) AS f1,
                                                SUM(CASE
                                                    WHEN l.level > 0 OR l.last_week_level > 0 THEN 1
                                                    ELSE 0
                                                END) AS f1_active
                                        FROM
                                            users AS l
                                        WHERE
                                            sponsor_id <> 0
                                        GROUP BY l.sponsor_id) AS f ON f.sponsor_id = tree.id"));
        $bar = $this->output->createProgressBar(count($users));
        // Get commmission of F1 level 0
        $headersCommissions = ['username', 'email', 'F1', 'F1.Active', 'Vol.Sub', 'Vol.Today', 'Com.sub','Com.Today','Com.Month'];
        $resultCommissions = [];
        foreach ($users as $value) {
            $bar->advance();
            Cache::put($value->email,$value);
            $resultCommissions[] = [
                $value->username,
                $value->email,
                $value->f1,
                $value->f1_active,
                $value->vol_subday,
                $value->vol_today,
                $value->com_subday,
                $value->com_today,
                $value->com_month
            ];
        }

        $bar->finish();
        $this->info('');
        $this->info('<]==[ LIST COMMISSION ]==[>');
        $this->table($headersCommissions, $resultCommissions);
    }
}
