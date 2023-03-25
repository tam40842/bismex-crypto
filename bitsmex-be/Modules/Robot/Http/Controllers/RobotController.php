<?php

namespace Modules\Robot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Robot\Entities\Robot;
use Modules\Robot\Entities\RobotOrder;
use Validator;
use Auth;
use DB;
use App\Http\Controllers\Vuta\Status;
use Modules\AntiCheat\Entities\Anti;

class RobotController extends Controller
{
    use Status;

    public function getRobots() {
        $robots = Robot::where('actived', 1)->orderBy('min', 'asc')->get();
        foreach($robots as $key => $value) {
            $check_status = RobotOrder::where('package_id', $value->id)->where('status', 1)->where('userid', Auth::id())->first();
            (!is_null($check_status)) ? $robots[$key]->user_robot = 1 : $robots[$key]->user_robot = 0;
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get robot packages success.',
            'data' => $robots
        ]);
    }
    
    public function Invesment(Request $request) {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        $package = DB::table('robot_packages')->where('id', $request->id_robot)->first();
        if(is_null($package)) {
            return response()->json([
                'status' => 422,
                'message' => 'The AI BOT Package does not exist.'
            ]);
        }
        $check_order = RobotOrder::where('userid', $user->id)->where('package_id', $package->id)->where('status', 1)->first();

        if($request->type == 'inactive' && !is_null($check_order)) {
            $check_order->update([
                'status' => 0,
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Inactive AI BOT success'
            ]);
        }else if($request->type == 'actived' && is_null($check_order)) {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'id_robot' => 'required|numeric',
            ]);
            if($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }

            $amount = abs((double)$request->amount);

            if($amount < $package->min) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The amount to invest min is $'.round($package->min, 2).' or more.'
                ]);
            }

            if($amount > $package->max) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The amount to invest max is $'.round($package->max, 2)
                ]);
            }

            if($amount > $user->live_balance) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Your balance has not enough.'
                ]);
            }
            
            DB::beginTransaction();
            try {
                DB::table('users')->where('id', $user->id)->lockForUpdate()->decrement('live_balance', $amount);
                RobotOrder::firstOrCreate([
                    'orderid' => strtoupper(uniqid('OR')),
                    'userid' => $user->id,
                    'package_id' => $package->id,
                    'amount' => (double)$amount,
                    'interest' => $package->interest,
                    'status' => 1
                ]);
                DB::commit();
                Anti::firewall($user->id, 'robot_order', [
                    'user' => $user,
                    'amount' => $amount,
                    'created_at' => date(now())
                ]);
                // // tính commission cho người giới thiệu
                // SendEmail::dispatch($recipient->email, 'Your AI BOT has just been activated', 'investment', [
                //     'user' => $user, 
                //     'amount' => $amount,
                //     'created_at' => date(now()),
                //     'package' => $package
                // ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'The '.$package->name.' has been actived.'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 422,
                    'message' => 'Order AI BOT has error',
                ]);
            }
        }

        return response()->json([
            'status' => 422,
            'message' => 'Order AI BOT has error'
        ]);
    }

    public function getMyPackages() {
        $user = DB::table('users')->where('status', 1)->where('id', Auth::id())->first();
        $orders = RobotOrder::join('robot_packages', 'robot_packages.id', '=', 'robot_order.package_id')->where('robot_order.userid', $user->id)->orderBy('robot_order.id', 'desc')->select('robot_order.*', 'robot_packages.name as name')->paginate(10);
        $status = $this->robot_status();
        foreach($orders as $key => $value) {
            $orders[$key] = $value;
            $orders[$key]->status_html = $status[$value->status];
        }
        return response()->json([
            'status' => 200,
            'message' => 'Get your robot packages is success.',
            'data' => $orders
        ]);
    }

    public function histories($orderid) {
        $orderid = strtoupper($orderid);
        $user = Auth::user();
        $robot = DB::table('robot_order')->where('userid', $user->id)->where('orderid', $orderid)->first();
        if(is_null($robot)) {
            return response()->json([
                'status' => 422,
                'message' => 'The AI BOT package does not exist.',
            ]);
        }
        $histories = DB::table('robot_commission_histories')->where('userid', $user->id)->where('robotid', $orderid)->orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $histories
        ]);
    }
}
