<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveDelegation extends Model
{
    protected $fillable = ['delegated_role_to', 'leave_id'];

    protected $appends = ['staff'];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function profile()
    {
        return $this->hasOne(StaffProfile::class, 'personal_id', 'delegated_role_to');
    }

    public function getStaffAttribute()
    {
        return StaffProfile::where('personal_id', $this->leave->personal_id)->first();
    }
}
