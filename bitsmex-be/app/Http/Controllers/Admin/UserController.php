<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use App\User;
use DB;
use App\Events\Verifed;
use App\Jobs\SendEmail;
use App\Twofa;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\Vuta\Vuta;
use App\Http\Controllers\Vuta\VutaMail;
use Gate;

class UserController extends Controller
{
    public function index(Request $request) {
        Gate::allows('modules', 'list_users_access');

        if(!isset($request->user_status) || $request->user_status == '') {
            $users = User::where('admin_setup', 0)->orderBy('id', 'desc')->paginate(100);
        } else {
            $users = User::where('admin_setup', 0)->where('status', intval($request->user_status))->orderBy('id', 'desc')->paginate(100);
        }
        $data = [
            'users' => $users,
            'roles' => config('roles'),
            'status' => $this->user_status(),
            'status_admin_setup' => $this->user_admin_setup_status(),
        ];
        return view('admin.users.index', $data);
    }

    public function export() {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function getAdd() {
        Gate::allows('modules', 'list_users_add');

        $data = [
            'user_status' => $this->user_status(),
            'action' => 'add',
            'currencies' => null
        ];
        return view('admin.users.add', $data);
    }

    public function postAdd(Request $request) {
        Gate::allows('modules', 'list_users_add');

        $this->validate($request, [
            'username' => 'required|unique:users',
            // 'first_name' => 'required|string',
            // 'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'status' => 'required',
            'roles' => 'required',
        ]);
        $code = $this->random_code();
        $data = [
            'ref_id' => $this->random_code(),
            'sponsor_code' => $code,
            'sponsor_ref' => $code,
            'sponsor_level' => 0,
            'username' => $request->username,
            // 'first_name' => $request->first_name,
            // 'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => intval($request->status),
            'roles' => json_encode($request->roles),
            'admin_setup' => is_null($request->admin_setup) ? 0 : 1,
            'live_balance' => isset($request->live_balance) ? $request->live_balance : 0
        ];
        if(isset($request->phone_number)) {
            $this->validate($request, [
                'phone_number' => 'required|unique:users|min:10|max:11'
            ]);
            $data['phone_number'] = $request->phone_number;
        }
        if(isset($request->address)) {
            $data['address'] = $request->address;
        }
        $twofa = new Twofa();
        $data['google2fa_secret'] = $twofa->createSecret();
        $user = User::create($data);
        return redirect()->route('admin.users.edit', ['id' => $user->id])->with('alert_success', 'The user has been created successfully.');
    }

    public function getEdit($id) {
        Gate::allows('modules', 'list_users_edit');

        $user = User::find($id);
        if(is_null($user)) { 
            return redirect()->back()->with('alert_error', 'The user does not exist.');
        }
        
        $users_code = explode('-', $user->sponsor_code);
        $users_down_line = DB::table('users')->whereIn('sponsor_ref', $users_code)->pluck('username')->toArray();

        $data = [
            'user' => $user,
            'user_status' => $this->user_status(),
            'sponsor' => User::find($user->sponsor_id),
            'wallets' => DB::table('wallet_address')->where('userid', $user->id)->get(),
            'recent_login' => DB::table('recent_logins')->where('userid', $user->id)->orderBy('id', 'desc')->paginate(5),
            'users_down_line' => $users_down_line,
            'stastics' => [
                'live_balance' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Live Balance',
                    'total' => $user->live_balance
                ],
                'total_deposit' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Total Deposit',
                    'total' => DB::table('deposit')->where('userid', $user->id)->where('status', 1)->sum('total')
                ],
                'total_withdraw' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Total Withdraw',
                    'total' => DB::table('withdraw')->where('userid', $user->id)->where('status', 1)->sum('total')
                ],
                'total_money_sent' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Total Money Sent',
                    'total' => DB::table('transfers')->where('userid', $user->id)->where('status', 1)->sum('total')
                ],
                'total_money_received' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Total Money Received',
                    'total' => DB::table('transfers')->where('recipient_id', $user->id)->where('status', 1)->sum('total')
                ],
                'trade_profit' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Trade Profit',
                    'total' => DB::table('orders')->where('type', 'live')->where('userid', $user->id)->select(DB::raw('IFNULL(SUM(CASE WHEN status = 1 THEN amount*profit_percent/100 ELSE 0 END) - SUM(CASE WHEN status = 2 THEN amount ELSE 0 END), 0) as profit'))->value('profit'),
                ],
                'commission' => [
                    'icon' => 'fa fa-usd',
                    'name' => 'Commission',
                    'total' => DB::table('commissions')->where('userid', $user->id)->where('status', 1)->sum('amount')
                ],
            ]
        ];

        return view('admin.users.edit', $data);
    }

    public function childView($sponsor_id, $level){       
        $users = DB::table('users')->where('sponsor_id', $sponsor_id)->get(); 
        $html ='<ul>';
        foreach ($users as $user) {
            $user_check = DB::table('users')->where('sponsor_id', $user->id)->get();
            if(!is_null($user_check)){
            $html .='<li class="tree-view closed"><a class="tree-name">'.$user->username.'</a>';                  
                $html.= $this->childView($user->id, $level+1);
            }else{
                $html .='<li class="tree-view"><a class="tree-name">'.$user->username.'</a>';                                 
                $html .="</li>";
            }
        }
        
        $html .="</ul>";
        return $html;
} 

    public function postEdit(Request $request, $id) {
        Gate::allows('modules', 'list_users_edit');

        $user = User::find($id);
        if(is_null($user)) {
            return redirect()->back()->with('alert_error', 'The user does not exist.');
        }
        $this->validate($request, [
            'username' => 'required|string',
            // 'email' => 'required|email|unique:users,email,'.$user->id,
            'status' => 'required',
            'roles' => 'required',
            'google2fa_enable' => 'required|boolean',
            // 'level' => 'boolean',
            'is_franchise' => 'boolean',
        ]);
        if($request->live_balance != $user->live_balance && $user->admin_setup) {
            $this->validate($request, [
                'live_balance' => 'required|numeric|min:0',
            ]);
        }
        
        foreach($request->roles as $value) {
            if(!array_key_exists($value, config('roles'))) {
                return redirect()->back()->with('alert_error', 'The user role is not valid.'); 
            }
        }

        $data = [
            'username' => $request->username,
            // 'email' => $request->email,
            'first_name' => @$request->first_name,
            'last_name' => @$request->last_name,
            'phone' => @$request->phone_number,
            'status' => intval($request->status),
            'roles' => json_encode($request->roles),
            'kyc_status' => @$request->kyc_status,
            'google2fa_enable' => $request->google2fa_enable,
            'live_balance' => $user->admin_setup ? $request->live_balance : $user->live_balance,
            // 'level' => (integer)$request->level,
        ];

        if(isset($request->is_franchise)) {
            if((integer) $request->is_franchise == 1){
                $data['is_franchise'] = 1;
                $data['last_week'] = Carbon::now()->weekOfYear;
                $data['last_date_week'] = Carbon::now()->endOfWeek();
                $data['last_week_level'] = 1;
                $data['join_franchise_date'] = Carbon::now();
            } else{
                $data['is_franchise'] = 0;
                $data['last_week'] = null;
                $data['last_date_week'] = null;
                $data['last_week_level'] = 0;
                $data['join_franchise_date'] = null;
            }
        }

        if(isset($request->password)) {
            $this->validate($request, [
                'password' => 'required|min:6',
            ]);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.users')->with('alert_success', 'The user has been updated successfully.');
    }

    public function getUpLine($id) {
        $user = DB::table('users')->where('id', $id)->first();
        if(!is_null($user)) {
            $users = DB::table('users')->where('sponsor_code', 'LIKE','%'.$user->sponsor_ref.'%')->select('id', 'sponsor_id as parent', 'username as text')->get()->toArray();
            foreach($users as $key => $value) {
                $users[$key] = [
                    'id' => $value->id,
                    'parent' => $value->id == $user->id ? '#' : $value->parent,
                    'text' => $value->text
                ];
            };
            
            return response()->json($users);
        }
    }

    public function getBanned($id) {
        $user = User::find($id);
        if(is_null($user)) {
            return redirect()->back()->with('alert_error', 'User does not exist.');
        }
        $status = $user->status != 2 ? 2 : 1;
        $user->update(['status' => $status]);
        return redirect()->route('admin.users')->with('alert_success', 'The user has been banned/unban successfully.');
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $users = User::where(function($query) use ($search_text) {
            $query->where('username', 'LIKE', '%'.$search_text.'%')->orWhere('email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('first_name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('last_name', 'LIKE', '%'.$search_text.'%')
            ->orWhere('email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('roles', 'LIKE', '%'.$search_text.'%');
        })->orderBY('id', 'desc')->paginate(100);
        $data = [
            'users' => $users,
            'roles' => config('roles'),
            'status' => $this->user_status(),
            'status_admin_setup' => $this->user_admin_setup_status(),
        ];
        return view('admin.users._item', $data)->render();
    }

    public function getVerify(Request $request) {
        Gate::allows('modules', 'verifing_users_access');

        $verifies = DB::table('users')
        ->join('kycs', 'users.id', '=', 'kycs.userid')
        ->whereIn('users.kyc_status', [1,2,3])
        ->select('users.*', 'kycs.*', 'kycs.created_at as verify_created_at', 'kycs.updated_at as verify_updated_at', 'kycs.id as verify_id', 'kycs.status as verifing_status')
        ->orderBy('kycs.id', 'desc')->paginate(100);
        $data = [
            'kycs' => $verifies,
            'status' => $this->admincp_verify_status()
        ];
        return view('admin.kyc.index', $data);
    }

    public function getVerifyEdit($id) {
        Gate::allows('modules', 'verifing_users_edit');

        $verifed = DB::table('kycs')->where('id', $id)->first();
        if(is_null($verifed)) {
            return redirect()->back()->with('alert_error', 'The document does not exist.');
        }
        $data = [
            'kyc' => $verifed,
            'user' => User::find($verifed->userid),
            'status' => $this->admincp_verify_status()
        ];
        return view('admin.kyc.edit', $data);
    }

    public function postVerifyEdit(Request $request, $id) {
        Gate::allows('modules', 'verifing_users_edit');

        $verifed = DB::table('kycs')->where('id', $id)->first();
        if(is_null($verifed)) {
            return redirect()->back()->with('alert_error', 'The document does not exist.');
        }
        $this->validate($request, [
            'status' => 'required',
        ]);
        $user = User::find($verifed->userid);
        if($user->kyc_status == 2) {
            return redirect()->back()->with('alert_error', 'This user has been approved.');
        }
        $reason = $request->has('reason') ? $request->reason : '';
        DB::table('kycs')->where('id', $id)->update([
            'reason' => $reason,
            'status' => intval($request->status)
        ]);
        $kyc_status = intval($request->status);
        $user->kyc_status = $kyc_status;
        $user->updated_at = date(now());
        $user->save();
        if($kyc_status == 2) {
            SendEmail::dispatch($user->email, 'Your verification has been accepted', 'verify_approved', ['user' => $user]);
        }
        if($kyc_status == 3) {
            SendEmail::dispatch($user->email, 'Your verification has not been accepted', 'verify_cancelled', ['user' => $user, 'reason' => $reason]);
        }
        return redirect()->route('admin.users.verifing')->with('alert_success', 'The document has been updated.');
    }

    public function getVerifyDelete($id) {
        Gate::allows('modules', 'verifing_users_delete');

        $verifed = UserVerifed::find($id);
        if(is_null($verifed)) {
            return redirect()->back()->with('alert_error', 'Verify does not exist.');
        }
        $verifed->delete();
        return redirect()->route('admin.users.verifing')->with('alert_success', 'Verify updated successfully.');
    }

    public function postVerifySearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $verifies = DB::table('users')
        ->join('kycs', 'users.id', '=', 'kycs.userid')
        ->where(function($query) use ($search_text) {
            $query->where('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.ref_id', 'LIKE', '%'.$search_text.'%');
        })
        ->where('kycs.status', '<>', 0)
        ->select('users.*', 'kycs.*', 'kycs.created_at as verify_created_at', 'kycs.updated_at as verify_updated_at', 'kycs.id as verify_id', 'kycs.status as verifing_status')
        ->orderBy('kycs.id', 'desc')->paginate(10);
        $data = [
            'kycs' => $verifies,
            'status' => $this->admincp_verify_status()
        ];
        return view('admin.kyc._item', $data)->render();
    }

    public function getBalance() {
        $users = DB::table('users')->join('user_balance', 'users.id', '=', 'user_balance.userid')->paginate(10);
        $data = [
            'users' => $users,
            'currencies' => Currencies::all(),
            'stastics' => []
        ];
        foreach($data['currencies'] as $key => $value) {
            $data['stastics'][$value->symbol] = DB::table('user_balance')->sum($value->symbol);
        }
        return view('admin.users.balance.index', $data);
    }

    public function getFilters(Request $request) {
        $this->validate($request,
        [
            'status' => 'required',
            'admin_setup' => 'required|boolean',
        ]);
        $users = User::where('status', $request->status)->where('admin_setup', $request->admin_setup)->orderBy('id', 'desc')->paginate(10);
        $data = [
            'users' => $users,
            'roles' => config('roles'),
            'status' => $this->user_status(),
            'status_admin_setup' => $this->user_admin_setup_status(),
            'filter' => [
                'status' => $request->status,
                'admin_setup' => $request->admin_setup
            ],
        ];
        return view('admin.users.index', $data);
    }

    public function richlist() {
        Gate::allows('modules', 'richlist_users_access');
        
        $users = DB::table('users')->where('admin_setup', 0)->select(DB::raw('(primary_balance + live_balance) as total_balance'), 'users.*')->orderBy('total_balance', 'desc')->paginate(100);
        $data = [
            'rich' => $users,
            'status' => $this->user_status()
        ];
        return view('admin.users.richlist.index', $data);
    }

    public function prolist() {
        Gate::allows('modules', 'prolist_users_access');

        $prolist = DB::table('orders')->join('users', 'users.id', '=', 'orders.userid')
                    ->where('users.admin_setup', 0)->where('orders.type', 'live')
                    ->groupBy('orders.userid')
                    ->select('users.username as username', 'users.id', 'users.status', 'users.live_balance', 
                    DB::raw("SUM( ( CASE WHEN orders.status = 1 THEN orders.amount * orders.profit_percent / 100 ELSE 0 END ) ) AS wintotal, 
                            SUM( ( CASE WHEN orders.status = 2 THEN orders.amount ELSE 0 END ) ) AS losetotal"))
                    ->orderBy('wintotal', 'desc')->paginate(100);
                    
        $sum = [
                'total_users' => [
                    'icon' => 'fa fa-users',
                    'type' => 'number',
                    'name' => 'Tổng User',
                    'total' => $prolist->total('id')
                ],
                'total_live_balance' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng Live Balance',
                    'total' => $prolist->sum('live_balance')
                ],
                'total_win' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng Win',
                    'total' => $prolist->sum('wintotal')
                ],
                'total_lose' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng Lose',
                    'total' => $prolist->sum('losetotal')
                ],
                'total_profit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng Profix',
                    'total' => $prolist->sum('wintotal') - $prolist->sum('losetotal')
                ],
            ];
        $data = [
            'prolist' => $prolist,
            'status' => $this->user_status(),
            'sum' => $sum,
        ];

        return view('admin.users.prolist.index', $data);
    }

    public function prolistPostSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        $prolist = DB::table('orders')->leftjoin('users', 'users.id', '=', 'orders.userid')->where('users.admin_setup', 0)->where('orders.type', 'live')->groupBy('orders.userid')->select('users.username as username', 'users.id', 'users.status', 'users.live_balance', DB::raw("SUM( ( CASE WHEN orders.status = 1 THEN amount ELSE 0 END ) ) AS wintotal, SUM( ( CASE WHEN orders.status = 2 THEN amount ELSE 0 END ) ) AS losetotal"))->where(function($query) use ($search_text) {
            $query->where('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.ref_id', 'LIKE', '%'.$search_text.'%');
        })->orderBy('wintotal', 'desc')->paginate(100);
        $data = [
            'prolist' => $prolist,
            'status' => $this->user_status()
        ];
        return view('admin.users.prolist._item', $data)->render();
    }

    public function prolistGetfilters(Request $request) {
        $today = date('Y-m-d H:i:s');
        if($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $filter = [
                ['orders.created_at', '>=', $date_from],
                ['orders.created_at', '<=', $date_to]
            ];
            $prolist = DB::table('orders')->leftjoin('users', 'users.id', '=', 'orders.userid')->where('type', 'live')->groupBy('orders.userid')->select('users.username as username', 'users.id', 'users.status', 'users.live_balance', DB::raw("SUM( ( CASE WHEN orders.status = 1 THEN amount ELSE 0 END ) ) AS wintotal, SUM( ( CASE WHEN orders.status = 2 THEN amount ELSE 0 END ) ) AS losetotal"))->whereBetween('orders.created_at', [$date_from, $date_to])->orderBy('wintotal', 'desc')->paginate(100);

            $data = [
                'prolist' => $prolist,
                'status' => $this->user_status(),
                'filter' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                ]
            ];
            return view('admin.users.prolist.index', $data);
        }
        return redirect()->route('admin.users.prolist');
    }

    public function getChildren ($user_id) {
        $list_children = DB::select(DB::raw("SELECT 
            id,
            username,
            volume,
            sponsor_id,
            REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
        FROM
            (SELECT 
                *
            FROM
                users) AS u,
            (SELECT @pv:=".$user_id.", @n:=0, @visit:='".$user_id.":0') initialisation
        WHERE
            FIND_IN_SET(sponsor_id, @pv)
                AND LENGTH(@pv:=CONCAT(@pv, ',', id))
                AND LENGTH(@tem:=@visit)
                AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
                        INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
                        LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))
        ORDER BY level ASC
        "));

        return $list_children;
    }
}
