<?php

namespace App;

use App\StaffProfile;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $connection = "auth_connection";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    protected $appends = ['permissions'];
    
    protected $append = ['permissions'];

    public function getPermissionsAttribute()
    {
        return collect(UserPermission::where('user_id', $this->id)->get())
                    ->map(function($d){ return $d->permission; })->values()->all();
    }


    public function profile()
    {
        return $this->hasOne(StaffProfile::class, 'email', 'email');
    }

}