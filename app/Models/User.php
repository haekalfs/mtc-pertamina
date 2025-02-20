<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    use TwoFactorAuthenticatable;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function users_detail(){
    	return $this->hasOne('App\Models\Users_detail')->withDefault();
    }

    public function role_id(){
    	return $this->hasMany('App\Models\Usr_role');
    }

    public function verifyTwoFactorCode($code)
    {
        $google2fa = new Google2FA();
        $secret = Crypt::decrypt($this->two_factor_secret);
        return $google2fa->verifyKey($secret, $code);
    }
}
