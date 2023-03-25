<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Commission\Entities\Level;
use DB;
use Carbon\Carbon;
use App\Jobs\SendEmail;

class LevelUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'level:up';

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
     * @return mixed
     */
    public $volume_percent = [75, 55, 35, 25, 15, 10, 5, 5, 5, 5];
    public $mb = 1000;

    public function handle() {
        $users = DB::table('users')->orderBy('id', 'desc')->get();
        $levels = DB::table('levels')->orderBy('level', 'asc')->get();
        foreach($users as $key => $user) {
            $branch_volume = $this->getBranchVolume($user->id, 0, 0);
            $branch_volume_mb = $branch_volume / $this->mb;
            $level = 0;
            $image = '';
            foreach($levels as $key => $lvl) {
                $f1_current_level = $lvl->level - 1;
                $have_f1_level = DB::table('users')->where('sponsor_id', $user->id)->where('level', $f1_current_level)->count();
                if($branch_volume_mb >= $lvl->condition_mb && $have_f1_level >= 2) {
                    $level = $lvl->level;
                    $image = $lvl->image;
                }
            }
            if($level > 0 && $user->level < $level) {
                DB::table('users')->where('id', $user->id)->update(['level' => $level]);
                SendEmail::dispatch($user->email, 'Congratulations on raising your ranks', 'levelup', ['user' => $user, 'level' => $level, 'image' => $image]);
            }
        }
    }

    public function getBranchVolume($userid, $volume, $floor) {
        $getChildren = DB::select(DB::raw("SELECT u.id as userid, IFNULL(SUM(amount),0) as volume FROM users u LEFT JOIN orders o  ON u.id = o.userid AND o.type = 'live' WHERE u.sponsor_id = ".$userid." GROUP BY u.id ORDER BY volume DESC"));
        foreach($getChildren as $key => $value) {
            if($floor > count($this->volume_percent)) {
                return round($volume, 2);
            }
            $percent = isset($this->volume_percent[$floor]) ? $this->volume_percent[$floor] : 0;
            $volume += $this->getBranchVolume($value->userid, $value->volume, $floor++) * $percent / 100;
        }
        return round($volume, 2);
    }
}
