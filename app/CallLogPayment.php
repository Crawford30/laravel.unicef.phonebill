<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLogPayment extends Model
{
    protected $fillable = [
        "bill_owner_id", 
        "user_data_id", 
        "personal_calls_count",
        "official_calls_count",
        "bill_type", 
        "identified_amount",
        "reviewed_amount", 
        "reviewed_by", 
        "status", 
        "call_log_for",
        "section",
        "to_date",
        "from_date",
        "document_one",
        "document_two",
        "invoice_status"
   ];


   public function phonebilluserdata()
    {
        return $this->belongsTo(PhoneBillUserData::class);
    }


}
