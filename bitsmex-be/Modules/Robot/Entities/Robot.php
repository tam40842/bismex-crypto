<?php

namespace Modules\Robot\Entities;

use Illuminate\Database\Eloquent\Model;

class Robot extends Model
{
    protected $table = 'robot_packages';

    protected $fillable = ['name', 'min', 'max', 'interest', 'actived'];
}
