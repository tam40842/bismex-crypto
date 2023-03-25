<?php

namespace Modules\Robot\Entities;

use Illuminate\Database\Eloquent\Model;

class RobotOrder extends Model
{
    protected $table = 'robot_order';

    protected $fillable = ['orderid', 'userid', 'package_id', 'amount', 'interest', 'status', 'created_at', 'updated_at'];
}
