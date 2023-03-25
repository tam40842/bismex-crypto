<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class WalletAddress extends Model
{
    protected $table = "wallet_address";
    protected $fillable = ['userid', 'symbol', 'input_address', 'destination_tag'];

    public function User() {
        return $this->BelongsTo(User::class, 'userid', 'id');
    }
}
