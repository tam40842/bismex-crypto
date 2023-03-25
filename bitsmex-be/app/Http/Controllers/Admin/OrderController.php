<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Orders;
use DB;
use Carbon\Carbon;
use Gate;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->listperpage = [
            10 => '10 item',
            50 => '50 item',
            100 => '100 item',
            200 => '200 item',
            500 => '500 item',
        ];
    }

    public function index(Request $request) {
        Gate::allows('modules', 'orders_access');

        $orders = DB::table('orders')->leftjoin('users', 'users.id', '=', 'orders.userid')->where('users.admin_setup', 0)
        ->where('orders.type', 'live')->select('orders.*', 'users.username as username', 'users.admin_setup')
        ->orderBy('orders.id', 'desc')->paginate(100);
        $data = [
            'orders' => $orders,
            // 'markets' => [],
            'markets' => DB::table('markets')->get(),
            'order_status' => $this->order_status(),
            'listperpage' => $this->listperpage,
            'status_admin_setup' => $this->user_admin_setup_status(),
            'sum' => [],
            'stastics' => [
                'win' => DB::table('orders')->where('type', 'live')->where('status', 1)->sum('amount'),
                'lose' => DB::table('orders')->where('type', 'live')->where('status', 2)->sum('amount')
            ]
        ];
        // foreach(DB::table('markets')->get() as $key => $value) {
        //     $data['sum'][$value->market_name] = DB::table('orders')->where('market_name', $value->market_name)->sum('amount');
        // }
        return view('admin.orders.index', $data);
    }

    public function getEdit($id) {
        Gate::allows('modules', 'orders_edit');

        $order = DB::table('orders')->where('id', $id)->first();
        if(is_null($order)) {
            abort('404');
        }
        $data = [
            'order' => $order,
            'user' => User::find($order->userid),
            'order_status' => $this->order_status(),
        ];
        return view('admin.orders.edit', $data);
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        
        $orders = DB::table('orders')->leftjoin('users', 'orders.userid', '=', 'users.id')->where('users.admin_setup', 0)->where(function($query) use ($search_text) {
            $query->where('orders.orderid', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.action', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.market_name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.round', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.amount', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.profit_percent', 'LIKE', '%'.$search_text.'%')
            ->orWhere('orders.type', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%');
        })->where('orders.type', 'live')->select('orders.*', 'users.username as username', 'users.admin_setup')->orderBY('id', 'desc')->paginate(100);
        $data = [
            'orders' => $orders,
            'markets' => [],
            // 'markets' => Markets::all(),
            'order_status' => $this->order_status(),
            'listperpage' => $this->listperpage,
            'sum' => [],
            'stastics' => [
                'win' => DB::table('orders')->where('type', 'live')->where('status', 1)->sum('amount'),
                'lose' => DB::table('orders')->where('type', 'live')->where('status', 2)->sum('amount')
            ]
        ];
        foreach(DB::table('markets')->get() as $key => $value) {
            $data['sum'][$value->market_name] = DB::table('orders')->where('market_name', $value->market_name)->sum('amount');
        }
        return view('admin.orders.index', $data)->render();
    }

    public function getFilters(Request $request) {
        if($request->has('start_day') && $request->has('end_day')) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            if(date($request->end_day) == date('Y-m-d')) {
                $end_day = date('Y-m-d H:i:s');
            }else {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            }
            $orders = DB::table('orders')->leftjoin('users', 'users.id', '=', 'orders.userid')
                    ->where('users.admin_setup', 0)
                    ->whereBetween('orders.created_at', [$start_day, $end_day])
                    ->where('orders.type', 'live')
                    ->select('orders.*', 'users.username as username', 'users.admin_setup');
            if ($request->market_name)
                $orders = $orders->where('market_name', $request->market_name);
            if ($request->action)
                $orders = $orders->where('action', $request->action);
            
                $orders = $orders->orderBy('orders.id', 'desc')->paginate(intval($request->perpage));
            $markets = DB::table('markets')->get();
            $data = [
                'orders' => $orders,
                'markets' => $markets,
                'order_status' => $this->order_status(),
                'filter' => [
                    'start_day' => $request->start_day,
                    'end_day' => $request->end_day,
                    'market_name' => $request->market_name,
                    'action' => $request->action,
                    'perpage' => $request->perpage,
                ],
                'listperpage' => $this->listperpage,
                'sum' => [],
                'stastics' => [
                    'win' => DB::table('orders')->where('type', 'live')->whereBetween('created_at', [$start_day, $end_day])->where('status', 1)->sum('amount'),
                    'lose' => DB::table('orders')->where('type', 'live')->whereBetween('created_at', [$start_day, $end_day])->where('status', 2)->sum('amount')
                ]
            ];
            foreach($markets as $key => $value) {
                $data['sum'][$value->market_name] = DB::table('orders')->whereBetween('created_at', [$start_day, $end_day])->where('action', 'BUY')->where('market_name', $value->market_name)->sum('amount');
            }
            $data['sum']['VND'] = DB::table('orders')->whereBetween('created_at', [$start_day, $end_day])->where('action', 'SELL')->sum('amount');
            return view('admin.orders.index', $data);
        }
        return redirect()->route('admin.orders');
    }

    public function lastround() {
        Gate::allows('modules', 'lastround_access');

        $histories = DB::table('hand_histories')->leftjoin('users', 'users.id', '=', 'hand_histories.author_id')->orderBy('hand_histories.id', 'desc')->select('hand_histories.*', 'users.username as username')->paginate(100);
        $data = [
            'histories' => $histories,
            'markets' => DB::table('markets')->get(),
            'listperpage' => $this->listperpage
        ];
        return view('admin.orders.lastround.index', $data);
    }

    public function getLastroundFilters(Request $request) {
        Gate::allows('modules', 'lastround_access');

        $today = date('Y-m-d H:i:s');
        if($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $filter = [
                ['lastround.created_at', '>=', $date_from],
                ['lastround.created_at', '<=', $date_to]
            ];
            $histories = DB::table('hand_histories')->leftjoin('users', 'users.id', '=', 'hand_histories.author_id')
            ->orderBy('hand_histories.id', 'desc')->select('hand_histories.*', 'users.username as username')
            ->whereBetween('hand_histories.created_at', [$date_from, $date_to]);

            if ($request->market_name != "")
                $histories = $histories->where('hand_histories.marketname', $request->market_name);
                            
                $histories = $histories->orderBy('hand_histories.id', 'desc')->paginate(intval($request->perpage));
            
            $data = [
                'histories' => $histories,
                'markets' => DB::table('markets')->get(),
                'listperpage' => $this->listperpage,
                'filter' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'market_name' => $request->market_name,
                    'action' => $request->action,
                    'perpage' => $request->perpage,
                ],
            ];
            return view('admin.orders.lastround.index', $data);
        }
        return redirect()->route('admin.orders.lastround');
    }

    public function postLastroundSearch(Request $request) {
        Gate::allows('modules', 'lastround_access');

        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $histories = DB::table('hand_histories')->leftjoin('users', 'users.id', '=', 'hand_histories.author_id')
        ->where(function($query) use ($search_text) {
            $query->where('hand_histories.round_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('hand_histories.marketname', 'LIKE', '%'.$search_text.'%')
            ->orWhere('hand_histories.open', 'LIKE', '%'.$search_text.'%')
            ->orWhere('hand_histories.high', 'LIKE', '%'.$search_text.'%')
            ->orWhere('hand_histories.low', 'LIKE', '%'.$search_text.'%')
            ->orWhere('hand_histories.close', 'LIKE', '%'.$search_text.'%');
        })->orderBy('hand_histories.id', 'desc')->select('hand_histories.*', 'users.username as username')->paginate(100);
        
        $data = [
            'histories' => $histories,
            'markets' => DB::table('markets')->get(),
            'listperpage' => $this->listperpage,
        ];
        
        return view('admin.orders.lastround.index', $data)->render();
    }

    public function getByRound($round) {
        Gate::allows('modules', 'lastround_access');
        
        $orders = DB::table('orders')->leftjoin('users', 'users.id', '=', 'orders.userid')->where('orders.round', $round)
        ->select('orders.*', 'users.username as username')->orderBy('orders.id', 'desc')->paginate(100);
        
        $data = [
            'orders' => $orders,
            'markets' => [],
            // 'markets' => Markets::all(),
            'order_status' => $this->order_status(),
            'listperpage' => $this->listperpage,
            'sum' => [],
            'stastics' => [
                'win' => DB::table('orders')->where('type', 'live')->where('orders.round', $round)->where('status', 1)->sum('amount'),
                'lose' => DB::table('orders')->where('type', 'live')->where('orders.round', $round)->where('status', 2)->sum('amount')
            ]
        ];
        foreach(DB::table('markets')->get() as $key => $value) {
            $data['sum'][$value->market_name] = DB::table('orders')->where('market_name', $value->market_name)->sum('amount');
        }
        return view('admin.orders.index', $data);
    }
}
