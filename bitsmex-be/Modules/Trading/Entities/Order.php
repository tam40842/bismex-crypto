<?php

namespace Modules\Trading\Entities;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['orderid', 'shapeid', 'action', 'market_name', 'userid', 'round', 'amount', 'profit_percent', 'type', 'chartmode', 'expired', 'open_price', 'close_price', 'status', 'expired_at'];
}
