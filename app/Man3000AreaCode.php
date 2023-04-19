<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Man3000AreaCode extends Model
{
    

    protected $fillable = [
        "user_data_id",
        "area_code",
        "total_monthly_cost",
        "identified_amount",
        "reviewed_amount"
    ];

}
