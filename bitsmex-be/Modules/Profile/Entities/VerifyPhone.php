<?php

namespace Modules\Profile\Entities;

use Illuminate\Database\Eloquent\Model;

class VerifyPhone extends Model
{
    protected $table = 'verify_phone';
    protected $fillable = ['userid', 'code'];
}
