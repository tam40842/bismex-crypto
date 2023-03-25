<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpLevelFranchise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'franchise:level {start_date?}';

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
        $this->info('Start up level franchise.');
        $current = Carbon::now()->format("Ymd");
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        if (!is_null($this->argument('start_date'))) {
            $startDate = Carbon::parse($this->argument('start_date'))->startOfWeek();
            $endDate = Carbon::parse($this->argument('start_date'))->endOfWeek();
        }
        $headersFranchise = ['Id', 'username', 'join_date'];
        $resultFranchise = [];
        $users = DB::select(DB::raw("SELECT u.id, u.username, MAX(t.created_at) as created_at
                                        FROM
                                            transactions AS t
                                            LEFT JOIN users AS u ON u.id = t.userid
                                        WHERE
                                            type = 'ACTIVE_FRANCHISE' AND t.created_at BETWEEN '" . $startDate . "' AND '" . $endDate . "'
                                        GROUP BY t.userid 
                                        ORDER BY id DESC"));
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $bar->advance();
            $this->processUpLevel($user);
            $resultFranchise[] = [$user->id,$user->username, $user->created_at];
        }

        $bar->finish();
        $this->info('');
        $this->info('<]==[ LIST USER JOIN FRANCHISE ]==[>');
        $this->info('');
        $this->table($headersFranchise, $resultFranchise);
        $this->info('');
        $this->info('The command was successful!');
    }

    public function processUpLevel($user){
        DB::beginTransaction();
        try{
            DB::table('users')->where('id', $user->id)->update([
                'is_franchise' => 1,
                'last_week' => Carbon::now()->weekOfYear,
                'last_date_week' => Carbon::now()->endOfWeek(),
                'last_week_level' => 1,
                'join_franchise_date' => Carbon::now()
            ]);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
