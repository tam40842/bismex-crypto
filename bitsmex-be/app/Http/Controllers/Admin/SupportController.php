<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use DB;
use App\Http\Controllers\Vuta\Status;
use Gate;

class SupportController extends Controller
{
    public function getList() {
        Gate::allows('modules', 'ticket_access');

        $tickets = DB::table('ticket')->join('users', 'ticket.userid', '=', 'users.id')
        ->select('ticket.*', 'users.username')->orderBy('id', 'desc')->paginate(100);
        $status = $this->ticket_status();
        return view('admin.ticket.index', compact('tickets', 'status'));
    }

    public function getEdit($ticketid) {
        Gate::allows('modules', 'ticket_edit');

        $tickets = DB::table('ticket')
        ->join('ticket_detail', 'ticket.id', '=', 'ticket_detail.ticketid')
        ->join('users', 'users.id', '=', 'ticket_detail.userid')
        ->where('ticket.ticketid', $ticketid)->select('ticket.subject', 'ticket_detail.*', 'users.username as username')->orderBy('ticket_detail.id', 'desc')->paginate(10);
        return view('admin.ticket.edit', ['tickets' => $tickets]);
    }

    public function postEdit(Request $request, $ticketid) {
        Gate::allows('modules', 'ticket_edit');

        $ticket = DB::table('ticket')->where('ticketid', $ticketid)->first();
        if(is_null($ticket)) {
            abort('404');
        }
        $this->validate($request,
        [
            'message' => 'required',
        ],
        [
            'message.required' => 'Message must not be blank.',
        ]);
        DB::table('ticket')->where('ticketid', $ticketid)->update([
            'status' => 2
        ]);
        DB::table('ticket_detail')->insert([
            'ticketid' => $ticket->id,
            'userid' => Auth::id(),
            'message' => $request->message,
            'created_at' => date(now()),
            'updated_at' => date(now()),
        ]);

        return redirect()->back()->with('success', 'The ticket you reply has been successful');
    }

    // public function getDelete($slug) {
    //     $ticket = DB::table('ticket')->where('slug', $slug)->first();
    //     if(is_null($ticket)) {
    //         abort('404');
    //     }
    //     DB::table('ticket')->where('slug', $slug)->delete();
    //     $ticket_detail = DB::table('ticket_detail')->where('ticketid', $ticket->id)->delete();

    //     return redirect()->route('admin.ticket.list')->with('success', 'Delete ticket successfully');
    // }

    public function postSearch(Request $request) {
        $search_text = $request->search_text;
        if(!isset($search_text)) {
            return response()->json([
                'error' => 1,
                'message' => 'Search text an empty.'
            ]);
        }
        $tickets = DB::table('ticket')->join('users', 'ticket.userid', '=', 'users.id')
        ->where(function($query) use ($search_text) {
            $query->where('ticket.subject', 'LIKE', '%'.$search_text.'%')
            ->orwhere('ticket.ticketid', 'LIKE', '%'.$search_text.'%')
            ->orWhere('users.username', 'LIKE', '%'.$search_text.'%');
        })->orderBY('ticket.id')->paginate(100);
        $status = $this->ticket_status();
        return view('admin.ticket.index', compact('tickets', 'status'))->render();
    }
}
