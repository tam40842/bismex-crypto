<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraw';
    protected $fillable = ['action', 'withdraw_id', 'userid', 'symbol', 'output_address', 'rate', 'amount', 'fee', 'total', 'txhash', 'status', 'author'];
}
