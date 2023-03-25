<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use SoftDeletes;
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $table = "roles";
    protected $fillable = ['name', 'slug'];
}
