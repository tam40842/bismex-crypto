<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Offers;
use App\User;
use App\Currencies;
use DB;

class OfferController extends Controller
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
        $offers = DB::table('offers')->leftjoin('users', 'offers.userid', '=', 'users.id');
        if($request->has('offer_status') && $request->offer_status != '-1') {
            $offers = $offers->where('offers.status', $request->offer_status);
        }
        $offers = $offers->select('users.username as username', 'offers.*')->orderBy('offers.id', 'desc')->paginate(100);
        $data = [
            'offers' => $offers,
            'status' => $this->offer_status(),
            'currencies' => Currencies::all(),
            'listperpage' => $this->listperpage,
            'sum' => []
        ];
        foreach(Currencies::all() as $key => $value) {
            $sum = DB::table('offers')->where('action', 'SELL')->where('symbol', $value->symbol);
            if($request->has('offer_status') && $request->offer_status != '-1') {
                $sum = $sum->where('status', $request->offer_status);
            }
            $data['sum'][$value->symbol] = $sum->sum('amount');
        }
        $vnd_sum = DB::table('offers')->where('action', 'BUY');
        if($request->has('offer_status') && $request->offer_status != '-1') {
            $vnd_sum = $vnd_sum->where('status', $request->offer_status);
        }
        $data['sum']['VND'] = $vnd_sum->sum('total');
        return view('admin.offers.index', $data); 
    }

    public function getEdit($offer_id) {
        $offer = DB::table('offers')->where('offer_id', $offer_id)->first();
        if(is_null($offer)) {
            return redirect()->route('admin.offers')->with('alert_error', 'The offer does not exist.');
        }
        $data = [
            'offer' => $offer,
            'user' => User::find($offer->userid),
            'action_type' => $this->action_type(),
            'offer_status' => $this->offer_status(),
            'order_list' => DB::table('orders')
            ->leftjoin('users', 'users.id', '=', 'orders.userid')
            ->where('orders.offer_id', $offer->offer_id)
            ->select('orders.*', 'users.username as username')
            ->orderBy('orders.id', 'desc')->paginate(10)
        ];
        return view('admin.offers.edit', $data);
    }

    public function postSearch(Request $request) {
        $search_text = isset($request->search_text) ? $request->search_text : '';
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'search text an empty.'
            ]);
        }
        
        $offers = DB::table('offers')->leftjoin('users', 'offers.userid', '=', 'users.id')->where(function($query) use ($search_text) {
            $query->where('offers.offer_id', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.phone_number', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.identity_number', 'LIKE', '%'.$search_text.'%');
        })->select('offers.*', 'users.username as username')->orderBY('id', 'desc')->paginate('10');
        $data = [
            'offers' => $offers,
            'status' => $this->offer_status(),
        ];
        return view('admin.offers._item', $data)->render();
    }

    public function getFilters(Request $request) {
        if($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $filter = [
                //['offers.created_at', '>=', $date_from],
                //['offers.created_at', '<=', $date_to]
            ];
               
            $offers = DB::table('offers')->leftjoin('users', 'offers.userid', '=', 'users.id')
                                            ->select('users.username as username', 'offers.*')
                                            ->whereBetween('offers.created_at', [$date_from, $date_to]);
            if ($request->symbol != "")
                $offers = $offers->where('symbol', $request->symbol);
            if ($request->action != "")
                $offers = $offers->where('action', $request->action);
            if ($request->offer_status != "" && $request->offer_status >= 0)
                $offers = $offers->where('offers.status', $request->offer_status);
             
            
            $offers = $offers->orderBy('offers.id', 'desc')->paginate(intval($request->perpage));

            $data = [
                'offers' => $offers,
                'status' => $this->offer_status(),
                'currencies' => Currencies::all(),
                'filter' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'symbol' => $request->symbol,
                    'action' => $request->action,
                    'offer_status' => $request->offer_status,
                    'perpage' => $request->perpage,
                ],
                'listperpage' => $this->listperpage,
                'sum' => []
            ];
            foreach(Currencies::all() as $key => $value) {
                $sum = DB::table('offers')->where('action', 'SELL')->where('symbol', $value->symbol);
                if($request->has('offer_status') && $request->offer_status != '-1') {
                    $sum = $sum->where('status', $request->offer_status);
                }
                $data['sum'][$value->symbol] = $sum->sum('amount');
            }
            $vnd_sum = DB::table('offers')->where('action', 'BUY');
            if($request->has('offer_status') && $request->offer_status != '-1') {
                $vnd_sum = $vnd_sum->where('status', $request->offer_status);
            }
            $data['sum']['VND'] = $vnd_sum->sum('total');
    
            return view('admin.offers.index', $data);
        }
        return redirect()->route('admin.offers');
    }
}
