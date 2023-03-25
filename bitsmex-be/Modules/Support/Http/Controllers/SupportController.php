<?php

namespace Modules\Support\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Auth;
use JWTAuth;
use App\Http\Controllers\Vuta\Status;
use App\Http\Controllers\Vuta\Vuta;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Validator;
use Modules\Support\Entities\Ticket;
use Modules\Support\Entities\TicketDetail;

class SupportController extends Controller
{
    use ValidatesRequests, Status, Vuta;

    public function getTickets() {
        $user = Auth::user();
        $ticket = Ticket::where('userid', $user->id)->OrderBy('id', 'desc')->limit(20)->get();
        // $status = $this->ticket_status();
        // foreach($ticket as $key => $value) {
        //     $ticket[$key] = $value;
        //     $ticket[$key]->status_html = $status[$value->status];
        // }
        return response()->json([
            'status' => 200,
            'message' => 'Get tickets data is success.',
            'data' => $ticket,
        ], 200);
    }

    public function createTicket(Request $request) {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1024',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $img = '';
        if($request->has('attach')) {
            $this->validate($request,
            [
                'attach' => 'mimes:jpeg,jpg,png,gif|max:20480'
            ],
            [
                'attach.image' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB',
                'attach.max' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB'
            ]);
            $file = $request->file('attach');
            $img = Vuta::images($file);
        }

        $user = Auth::user();
        $ticket = Ticket::firstOrCreate([
            'ticketid' => strtoupper(uniqid('TICKET')),
            'userid' => $user->id,
            'subject' => $request->subject,
        ]);
        TicketDetail::create([
            'ticketid' => $ticket->id,
            'userid' => $user->id,
            'message' => $request->message,
            'image' => $img
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your ticket has been sent successfully.',
        ], 200);
    }

    public function getDetail($ticketid) {
        $ticketid = strtoupper($ticketid);
        $user = Auth::user();
        $tickets = Ticket::join('ticket_detail', 'ticket_detail.ticketid', '=', 'ticket.id')->where('ticket.userid', $user->id)->where('ticket.ticketid', $ticketid)->select('ticket.subject', 'ticket.status as ticket_status', 'ticket_detail.*')->orderBy('ticket_detail.id', 'desc')->paginate(10);
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $tickets
        ], 200);
    }

    public function Reply(Request $request) {
        $validator = Validator::make($request->all(), [
            'ticketid' => 'required|string|max:255',
            'message' => 'required|string|max:1024',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $img = '';
        if($request->has('attach')) {
            $this->validate($request,
            [
                'attach' => 'mimes:jpeg,jpg,png,gif|max:20480'
            ],
            [
                'attach.image' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB',
                'attach.max' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB'
            ]);
            $file = $request->file('attach');
            $img = Vuta::images($file);
        }

        $user = Auth::user();
        $ticket = Ticket::where('userid', $user->id)->where('ticketid', $request->ticketid)->first();
        if(is_null($ticket)) {
            return response()->json([
                'status' => 422,
                'message' => 'The ticket does not exist.'
            ], 200);
        }
        if($ticket->status == 3) {
            return response()->json([
                'status' => 422,
                'message' => 'The ticket has been closed.'
            ], 200);
        }
        TicketDetail::firstOrCreate([
            'ticketid' => $ticket->id,
            'userid' => $user->id,
            'message' => (string)$request->message,
            'image' => $img
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your reply message has been sent.',
        ], 200);
    }
}
