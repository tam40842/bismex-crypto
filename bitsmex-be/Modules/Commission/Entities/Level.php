<?php

namespace Modules\Commission\Entities;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';
    protected $fillable = ['level_name', 'level', 'volume_personal', 'income_price', 'percent'];
}
