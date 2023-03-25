<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendEmail;
use Gate;
use DB;

class BulkMail extends Controller
{
    public function index() {
        Gate::allows('modules', 'bulkmail_access');

        return view('admin.bulkmail.index');
    }

    public function Send(Request $request) {
        Gate::allows('modules', 'bulkmail_add');

        $this->validate($request, [
            'type' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        $emails = [];
        if($request->type == 'email') {
            $this->validate($request, ['email' => 'required']);
            $emails = explode(',', $request->email);
        } else {
            $emails = DB::table('users')->orderBy('id', 'asc')->pluck('email');
        }
        foreach($emails as $email) {
            SendEmail::dispatch($email, $request->subject, 'bulk', ['message' => $request->message]);
        }
        return redirect()->route('admin.bulkmail')->with('alert_success', 'Gửi email thành công.');
    }
}
