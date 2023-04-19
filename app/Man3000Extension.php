<?php

namespace App;

use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Man3000Extension extends Model
{
    //

    protected $fillable = [
        "user_data_id",
        "name",
        "area_code",
        "total_monthly_cost",
        "mobile_number_unique_count",
        "identified_amount",
        "reviewed_amount"


    ];


    protected $appends = [
        'current_status', 'extension_count','unique_mobile_count','total_user_monthly_cost', 'bill_type'
    ];

    public function phoneBillUserData()
    {
        return $this->belongsTo(PhoneBillUserData::class);
    }

    public function phonebillext()
    {

        return $this->belongsTo(PhoneBillExtensions::class);
    }


    public function  phoneextensions()
    {

        return $this->belongsTo(Man3000Extension::class, "name");
    }


    public function getCurrentStatusAttribute()
    {

        return (CallLogStatus::where('user_data_id', $this->user_data_id)->where('bill_owner_id', auth()->user()->id)->orderBy('created_at', 'DESC')->first());
    }


    public function getBillTypeAttribute()
    {
          $callType = CallLogPayment::whereNotNull('bill_type')->where("bill_owner_id",  auth()->user()->id)->where("user_data_id", $this->user_data_id)->first();
        return $callType;
    }



    public function getExtensionCountAttribute()
    {

        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();

        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->get();

            return count($userExtensions);
        }


        //$userExtensions;


       
    }


    public function getUniqueMobileCountAttribute()
    {
        //unique_mobile_count
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
            $userCost = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $this->user_data_id)->sum('cost');
           // $sum = phoneBillExtension::where('user_data_id', '=', $singleStaffExten['user_data_id'])->where('area_code', '=', $singleStaffExten['area_code'])->sum('cost');
            return   round($userCost, 2);
        }

        
       
    }



}
