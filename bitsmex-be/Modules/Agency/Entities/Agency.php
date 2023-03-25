<?php

namespace Modules\Agency\Entities;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $table = "binary_tree";
    
    protected $fillable = ['userid', 'sponsor_id'];
}
