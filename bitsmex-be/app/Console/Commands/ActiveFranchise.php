<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActiveFranchise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'franchise:active';

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
        $this->info('Start up level 1 to franchise.');
        $current = Carbon::now()->format("Ymd");
        $headersFranchise = ['Id', 'username', 'join_date'];
        $resultFranchise = [];
        $users = DB::table('users')->where('status', 1)->where('level', 1)->get();
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $bar->advance();
            $this->processUpLevel($user);
            $resultFranchise[] = [$user->id,$user->username, $user->created_at];
        }

        $bar->finish();
        $this->info('');
        $this->info('<]==[ LIST USER LEVL 1 TO FRANCHISE ]==[>');
        $this->info('');
        $this->table($headersFranchise, $resultFranchise);
        $this->info('');
        $this->info('The command was successful!');
    }

    public function processUpLevel($user){
        DB::beginTransaction();
        try{
            DB::table('users')->where('id', $user->id)->where('level', 1)->update([
                'is_franchise' => 1,
                'last_week' => Carbon::now()->weekOfYear,
                'last_date_week' => Carbon::now()->endOfWeek(),
                'last_week_level' => 1
            ]);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
        }
    }
}
