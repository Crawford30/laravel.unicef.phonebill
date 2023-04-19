<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLogStatus extends Model
{
    protected $fillable = [
        "user_data_id",
        "bill_owner_id",
        "call_log_with",
        "personal_count",
        "official_count",
        "total_count"
    ];



    public function getCreatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }
}
