<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneBillExtensions extends Model
{
    //

    protected $fillable = [
        "user_id", "user_data_id", "bill_owner_id",  "ext", "area_code", "phone_number","name", "type",
         "date_time",  "duration", "cost", "call_type", "is_call_type_accepted",
          "is_call_type_accepted", "status",   
   ];
  


   public function userphonebilldata()
   {
    
       return $this->belongsTo(PhoneBillUserData::class, "ext", "user_data_id");
   }



   public function man3000Extensions()
   {
       return $this->hasMany(Man3000Extension::class, "ext");
   }
   


 
  
}
