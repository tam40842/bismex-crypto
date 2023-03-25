<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $table = "permissions";
    protected $fillable = ['id_role', 'slug_module'];
}
