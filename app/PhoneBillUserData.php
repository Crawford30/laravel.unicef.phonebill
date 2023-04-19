<?php

namespace App;
use App\Extension;
use App\Man3000Extension;
use App\PhoneBillExtensions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PhoneBillUserData extends Model
{
    //

    protected $fillable = [
        "identification_deadline_date",
        "user_id",
        "from_date",
        "to_date",
        "extensions",
        "total_monthly_cost",
        "unique_mobile_number_count",
        "unique_extensions_count",
        "total_records",

    ];

    //  protected $appends = ['current_status'];

     

    protected $appends = [
        'current_status', 'extension_count', 'unique_mobile_count', 'total_user_monthly_cost'
    ];


    protected $casts = [
        'extensions' => 'array',
    ];


    public function man3000Extension()
    {
        return $this->hasMany(Man3000Extension::class);
    }

   
    public function phonebillextension()
    {
        return $this->hasMany(phoneBillExtensions::class);
    }


  

    

    public function getExtensionCountAttribute()
    {
        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->get();
            return count($userExtensions);
        }
       
    }


    public function getUniqueMobileCountAttribute()
    {
        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->get();
            $count = DB::table('phone_bill_extensions')->where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->count(DB::raw('DISTINCT phone_number'));
            return $count;
        }
       
    }
    
    
    public function getTotalUserMonthlyCostAttribute()
    {

        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->sum('cost');
           // $sum = phoneBillExtension::where('user_data_id', '=', $singleStaffExten['user_data_id'])->where('area_code', '=', $singleStaffExten['area_code'])->sum('cost');
            return   $userExtensions;
        }
  
    }

    // public function getBillTypeAttribute()
    // {
    //       $callType = CallLogPayment::whereNotNull('bill_type')->where("bill_owner_id",  auth()->user()->id)->where("user_data_id", $this->user_data_id)->first();
    //     return $callType;
    // }

  





    public function callLogPayment()
    {
        return $this->hasOne(CallLogPayment::class);
    }



    public function getCurrentStatusAttribute()
    {
       
        return (CallLogStatus::where('user_data_id', $this->id)->where('bill_owner_id', auth()->user()->id)->orderBy('created_at', 'DESC')->first());
    }



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



