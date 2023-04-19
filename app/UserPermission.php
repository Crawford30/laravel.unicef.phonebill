<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $connection = "auth_connection";

    protected $hidden = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
