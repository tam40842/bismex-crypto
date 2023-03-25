<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\User;
use App\Currencies;
use DB;
use Storage;
use Gate;

class RobotController extends Controller
{
    public function index(Request $request) {
        Gate::allows('modules', 'robots_access');

        $robots = DB::table('robot_packages')->orderBy('actived', 'desc')->orderBy('max', 'asc')->paginate('10');
        $histories_bonus = DB::table('robot_commission_histories')
        ->join('robot_order', 'robot_commission_histories.robot_code', '=', 'robot_order.robot_code')
        ->join('robot_packages', 'robot_order.package_id', '=', 'robot_packages.id')
        ->join('users', 'robot_commission_histories.userid', '=', 'users.id')
        ->select('robot_commission_histories.*', 'robot_order.robot_code', 'robot_order.fee', 'robot_packages.name', 'robot_packages.id as package_id', 'users.username')->orderBy('robot_commission_histories.created_at', 'desc')->paginate(10);

        $data = [
            'robots' => $robots,
            'status' => $this->robot_status(),
            'histories_bonus' => $histories_bonus,
            'status_bonus_robot' => $this->status_bonus_robot()
        ];
        return view('admin.robots.index', $data);
    }
    
    public function getAdd() {
        Gate::allows('modules', 'robots_add');

        $data = [
            'level' => DB::table('robot_level_commission')->get(),
        ];
        return view('admin.robots.add', $data);
    }

    public function postAdd(Request $request) {
        Gate::allows('modules', 'robots_add');

        $this->validate($request, [
            'name' => 'required',
            'interest' => 'required|min:0|numeric',
            'actived' => 'required|boolean',
            'min' => 'required|min:0|integer',
            'max' => 'required|min:0|integer',
            // 'image' => 'required|url',
            'month' => 'required|min:0|integer',
            'bonus' => 'required|min:0|numeric',
            'fee' => 'required|min:0|numeric'
        ]);
        $data = [
            'name' => $request->name,
            // 'image' => $request->image,
            'min' => $request->min,
            'max' => $request->max,
            'interest' => $request->interest,
            'bonus' => $request->bonus,
            'fee' => $request->fee,
            'month' => $request->month,
            'actived' => intval($request->actived),
            'updated_at' => now(),
        ];
        
        DB::table('robot_packages')->insert($data);
        
        return redirect()->route('admin.robots')->with('alert_success', 'Tạo robot trade thành công.');
    }

    public function getEdit($id) {
        Gate::allows('modules', 'robots_edit');

        $robot = DB::table('robot_packages')->where('id', $id)->first();
        if(is_null($robot)) {
            return redirect()->back()->with('alert_error', 'Robot không tồn tại.');
        }
        $histories = DB::table('robot_order')->join('users', 'robot_order.userid', '=', 'users.id')
                    ->where('robot_order.package_id', $robot->id)->orderBy('robot_order.status', 'desc')->orderBy('robot_order.created_at', 'desc')
                    ->select('robot_order.*', 'users.username', 'users.sponsor_id')->paginate(10);

        foreach($histories as $key => $value) {
            if($value->sponsor_id > 0) {
                $user_bonus = DB::table('commissions')->join('users', 'commissions.userid', '=', 'users.id')
                                ->where('commissions.userid', $value->sponsor_id)->where('commissions.message', 'LIKE', '%'.$value->robot_code.'%')
                                ->select('commissions.amount', 'users.username')->first();

                if(!is_null($user_bonus)) {
                    $value->user_bonus = $user_bonus->username;
                    $value->amount_bonus = $user_bonus->amount;
                };
            }
        }

        $data = [
            'robot' => $robot,
            'robot_status' => $this->robot_status(),
            'histories' => $histories,
            'status_robot' => $this->status_robot()
        ];
        return view('admin.robots.edit', $data);
    }

    public function postEdit(Request $request, $id) {
        Gate::allows('modules', 'robots_edit');
        
        $robot = DB::table('robot_packages')->where('id', $id)->first();
        if(is_null($robot)) {
            return redirect()->back()->with('alert_error', 'Robot không tồn tại.');
        }
        $this->validate($request, [
            'name' => 'required',
            'interest' => 'required|min:0|numeric',
            'actived' => 'required|boolean',
            'min' => 'required|min:0|integer',
            'max' => 'required|min:0|integer',
            // 'image' => 'required|url',
            'month' => 'required|min:0|integer',
            'bonus' => 'required|min:0|numeric',
            'fee' => 'required|min:0|numeric'
        ]);
        $data = [
            'name' => $request->name,
            // 'image' => $request->image,
            'min' => $request->min,
            'max' => $request->max,
            'interest' => $request->interest,
            'fee' => $request->fee,
            'bonus' => $request->bonus,
            'month' => $request->month,
            'actived' => intval($request->actived),
            'updated_at' => now(),
        ];
        
        DB::table('robot_packages')->where('id', $id)->update($data);
        
        return redirect()->route('admin.robots')->with('alert_success', 'Cập nhật robot trade thành công.');
    }

    public function InfoCode($robot_code) {
        $robot_order = DB::table('robot_order')->join('robot_packages', 'robot_order.package_id', '=', 'robot_packages.id')
        ->join('users', 'robot_order.userid', '=', 'users.id')
        ->where('robot_code', $robot_code)->select('robot_order.*', 'robot_packages.name', 'users.username', 'users.sponsor_id')->first();
        if(is_null($robot_code)) {
            return redirect()->back()->with('alert_error', 'Gói robot này không tồn tại');
        }

        if($robot_order->sponsor_id > 0) {
            $user_bonus = DB::table('commissions')->join('users', 'commissions.userid', '=', 'users.id')
                        ->where('commissions.userid', $robot_order->sponsor_id)->where('commissions.message', 'LIKE', '%'.$robot_order->robot_code.'%')
                        ->select('commissions.*', 'users.username')->first();
        }

        $histories_bonus = DB::table('robot_commission_histories')->where('robot_code', $robot_order->robot_code)->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'robot_order' => $robot_order,
            'user_bonus' => isset($user_bonus) ? $user_bonus : null,
            'status_robot' => $this->status_robot(),
            'histories_bonus' => $histories_bonus,
            'status_bonus_robot' => $this->status_bonus_robot()
        ];
        return view('admin.robots.histories', $data);
    }

    // public function getDelete($id) {
    //     $robot = DB::table('robot_packages')->where('id', $id);
    //     if(is_null($robot->first())) {
    //         return redirect()->back()->with('alert_error', 'Robot không tồn tại.');
    //     }
    //     $robot->delete();
    //     return redirect()->route('admin.robots')->with('alert_success', 'Xóa robot thành công.');
    // }

    // public function postSearch(Request $request) {
    //     $search_text = $request->search_text;
    //     if(!isset($search_text)) {
    //         return response()->json([
    //             'error' => 1,
    //             'message' => 'search text an empty.'
    //         ]);
    //     }
    //     $robots = DB::table('robot_packages')->where(function($query) use ($search_text) {
    //         $query->where('name', 'LIKE', '%'.$search_text.'%')
    //         ->orwhere('amount', 'LIKE', '%'.$search_text.'%')
    //         ->orwhere('number_day', 'LIKE', '%'.$search_text.'%')
    //         ->orwhere('interest', 'LIKE', '%'.$search_text.'%');
    //     })->orderBy('id', 'desc')->paginate('10');
    //     $data = [
    //         'robots' => $robots,
    //         'status' => $this->robot_status()
    //     ];
    //     return view('admin.robots._item', $data)->render();
    // }
}
