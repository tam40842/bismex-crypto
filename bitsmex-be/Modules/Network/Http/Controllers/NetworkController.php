<?php

namespace Modules\Network\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\User;
use Auth;
use DB;

class NetworkController extends Controller
{
    public function getTree() {
        $user = Auth::user();
        return response()->json([
            'status' => 200,
            'message' => 'Get Sponsor tree successful',
            'data' => [
                'tree' => [
                    "name" => $user->username,
                    "avatar" => 'https://ui-avatars.com/api/?size=128&name='.$user->username,
                    "isOpen" => true,
                    "isParent" => true,
                    "children" => json_encode($this->get_sponsorTree($user->id))
                ]
            ]
        ]);
    }

    public static function get_sponsorTree($userid, $level = 0){
		$users = DB::table('users')->where('sponsor_id', $userid)->get();
		$level = $level + 1;
		$data = [];
		foreach($users as $key => $value) {
            if($level > 10) {
                break;
            }
			$data[] = [
                "name" => $value->username,
                "avatar" => 'https://ui-avatars.com/api/?size=128&name='.$value->username,
                "isOpen" => false,
                "isParent" => true,
				"children" => json_encode(self::get_sponsorTree($value->id, $level))
			];
		}
		return $data;
	}
}
