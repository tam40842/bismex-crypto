<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;

class TransferWallet extends Model
{
    protected $table = 'wallet_transfers';
    protected $fillable = ['userid', 'amount', 'from_wallet', 'to_wallet', 'status'];
}
