<?php

namespace Modules\Support\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $table = 'ticket_detail';
    protected $fillable = ['ticketid', 'userid', 'message', 'author_id'];
}
