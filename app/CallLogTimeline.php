<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLogTimeline extends Model
{
    protected $fillable = [
         "user_data_id",
         "call_log_payment_id", 
         "identified_by_id", 
         "date_uploaded", 
         "date_identified",
         "identified_by",  
         "date_reviewed", 
         "payment_amount",
         "payment_by",
         "payment_notification"   
   ];
}


