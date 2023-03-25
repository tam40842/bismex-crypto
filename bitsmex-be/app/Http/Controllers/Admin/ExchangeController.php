<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ExchangeController extends Controller
{
    public function index() {
        $data = [
            'exchange' => DB::table('exchange as e')
            ->leftjoin('users as u', 'e.userid', '=', 'u.id')
            ->orderBy('id', 'desc')->select('e.*', 'u.username')->paginate(100),
        ];
        return view('admin.exchange.index', $data);
    }

    public function getEdit(Request $request, $transfers_id) {
        $transfers = DB::table('transfers')->where('transfers.transfer_id', $transfers_id)
        ->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'u.id')
        ->select('transfers.*', 's.username as sender', 'u.username as receiver')->first();
        if(is_null($transfers)) {
            return redirect()->route('admin.transfers')->with('alert_error', 'The transferss does not exists.');
        }
        $data = [
            'transfers' => $transfers,
            'user' => DB::table('users')->where('id', $transfers->userid)->first(),
            'transfers_status' => $this->transfers_status(),
        ];
        return view('admin.transfers.edit', $data);
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $transfers = DB::table('transfers')->leftjoin('users as s', 'transfers.userid', '=', 's.id')->leftjoin('users as r', 'transfers.recipient_id', '=', 'u.id')->where(function($query) use ($search_text) {
            $query->where('transfers.transfer_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('transfers.amount', 'LIKE', '%'.$search_text.'%')
            ->orWhere('u.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('s.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('u.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('s.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('u.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('s.phone_number', 'LIKE', '%'.$search_text.'%');
        })->select('transfers.*', 's.username as sender', 'u.username as receiver')->orderBY('transfers.id', 'desc')->paginate(100);
        
        $data = [
            'transfers' => $transfers,
            'transfers_status' => $this->transfers_status(),
        ];
        return view('admin.transfers.index', $data)->render();
    }

    public function getFilters(Request $request) {
        if($request->has('start_day') && $request->has('end_day')) {
            $start_day = Carbon::parse($request->start_day)->startOfDay()->toDateTimeString();
            if(date($request->end_day) == date('Y-m-d')) {
                $end_day = date('Y-m-d H:i:s');
            }else {
                $end_day = Carbon::parse($request->end_day)->startOfDay()->toDateTimeString();
            }
            $exchange = DB::table('exchange as e')->leftjoin('users as u', 'e.recipient_id', '=', 'u.id')
            ->whereBetween('e.created_at', [$start_day, $end_day])
            ->select('e.*', 'u.username')->orderBy('e.id', 'desc')->paginate($request->paginate);
            $data = [
                'exchange' => $exchange,
                'filter' => [
                    'start_day' => $request->start_day,
                    'end_day' => $request->end_day,
                    'paginate' => $request->paginate,
                ]
            ];
            return view('admin.transfers.index', $data);
        }
        return redirect()->route('admin.withdraw');
    }
}
