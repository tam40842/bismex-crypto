<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    protected $table = "kycs";
    protected $fillable = ['userid', 'identity_frontend', 'identity_backend', 'selfie', 'identity_approved', 'selfie_approved', 'reason', 'status']; 
}
