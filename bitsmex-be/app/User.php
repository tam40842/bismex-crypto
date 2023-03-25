<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ref_id', 'sponsor_id', 'sponsor_code', 'sponsor_ref', 'sponsor_level', 'first_name','last_name', 'username', 'birthday', 'email', 'email_verified_at', 'password', 'phone_number', 'identity_number', 'gender', 'country', 'roles', 'permission', 'is_leader', 'ref_commission', 'remember_token', 'google2fa_secret', 'google2fa_enable', 'status', 'kyc_status', 'phone_status', 'live_balance', 'primary_balance', 'demo_balance', 'bonus_balance', 'robot_profit_balance', 'autotrade_balance', 'robot_bonus_balance', 'mmb_balance', 'avatar', 'play_mode', 'markets', 'market_active', 'level', 'level_current', 'volume', 'bonus_balance', 'prior_level', 'admin_setup', 'last_day_level',
        'is_franchise',
        'join_franchise_date',
        'last_week',
        'last_week_level',
        'last_reset_level',
        'last_date_week',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function Role() {
        return $role = DB::table('roles')->where('slug', $this->permission)->first();
    }

    public function total_volume() {
        return DB::table('orders')->where('type', 'live')->where('userid', $this->id)->sum('amount');
    }
}
