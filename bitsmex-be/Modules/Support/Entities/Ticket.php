<?php

namespace Modules\Support\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class Ticket extends Model
{
    protected $table = 'ticket';
    protected $fillable = ['id', 'ticketid',  'userid', 'subject', 'viewers', 'status'];
}
