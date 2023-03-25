<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfers';
    protected $fillable = ['transfer_id', 'action', 'userid', 'recipient_id', 'amount', 'fee', 'total', 'status'];
}
