<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Gate;

class CommissionHistories extends Controller
{
    public function index() {
        Gate::allows('modules', 'finance_commissions_access');

        $commissions = DB::table('commissions')->leftjoin('users', 'users.id', '=', 'commissions.userid')
        ->where('users.admin_setup', 0)
        ->select('commissions.*', 'users.username as username')
        ->orderBy('commissions.id', 'DESC')->paginate(100);
        
        $data = [
            'commissions' => $commissions,
            'commission_type' => [
                'trade' => 'Trade Commission',
                'bonus' => 'Bonus Commission',
            ],
        ];
        foreach($data['commission_type'] as $key => $value) {
            $data['commission_sum'][$key] = DB::table('commissions')->where('commission_type', $key)->sum('amount');
        }
        return view('admin.commissions.index', $data);
    }
    
    public function getFilters(Request $request) {
        $commissions = DB::table('commissions')->join('users', 'users.id', '=', 'commissions.userid')->where('users.admin_setup', 0);
        if($request->has('start_day') && $request->start_day != '' && $request->has('end_day') && $request->end_day != '' ) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            $commissions = $commissions->whereBetween('commissions.created_at', [$start_day, $end_day]);
        }
        if($request->has('type') && $request->type) {
            $commissions = $commissions->where('commission_type', $request->type);
        }
        $commissions = $commissions->select('commissions.*', 'users.username as username')->orderBy('commissions.id', 'DESC')->paginate(100);
        
        $data = [
            'commissions' => $commissions,
            'commission_type' => [
                'trade' => 'Trade Commission',
                'bonus' => 'Bonus Commission',
            ],
            'filter' => [
                'start_day' => $request->start_day,
                'end_day' => $request->end_day,
                'paginate' => $request->paginate,
            ],
        ];
        foreach($data['commission_type'] as $key => $value) {
            $data['commission_sum'][$key] = DB::table('commissions')->where('commission_type', $key)->sum('amount');
        }
        return view('admin.commissions.index', $data);
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $commissions = DB::table('commissions')->leftjoin('users', 'users.id', '=', 'commissions.userid')->where('users.admin_setup', 0)
        ->where(function($query) use ($search_text) {
            $query->where('users.username', 'LIKE', '%'.$search_text.'%')
            ->orwhere('commissions.amount', 'LIKE', '%'.$search_text.'%')
            ->orwhere('commissions.message', 'LIKE', '%'.$search_text.'%')
            ->orwhere('commissions.yearweek', 'LIKE', '%'.$search_text.'%');
        })->select('commissions.*', 'users.username as username')
        ->orderBy('commissions.id', 'desc')->paginate(100);
        
        $data = [
            'commissions' => $commissions,
            'commission_type' => [
                'trade' => 'Trade Commission',
                'bonus' => 'Bonus Commission',
            ],
        ];
        foreach($data['commission_type'] as $key => $value) {
            $data['commission_sum'][$key] = DB::table('commissions')->where('commission_type', $key)->sum('amount');
        }
        return view('admin.commissions.index', $data);
    }
}
