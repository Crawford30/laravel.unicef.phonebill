<?php

namespace App\Http\Controllers;

use App\CallLogPayment;
use Illuminate\Http\Request;
use App\IdentifiedUserPhoneBill;
use App\OtherAllowance;
use App\PhoneBillExtensions;
use App\PhoneBillUserData;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $hasSuperAdminPermission = in_array('s_admin', auth()->user()->permissions);

      

        $billOwnerData =   CallLogPayment::where('bill_owner_id',  auth()->user()->id)->select('document_one', 'document_two')->get();

        $docOne =    $billOwnerData->pluck('document_one')->first();
        $docTwo =    $billOwnerData->pluck('document_two')->first();
        
       

        $currentuserdata = PhoneBillUserData::leftJoin('call_log_payments', 'phone_bill_user_data.id', '=', 'call_log_payments.user_data_id')->where('call_log_payments.status', 'Reviewed')->where('call_log_payments.bill_type', 'UNICEF BANK ACCOUNT')->where('call_log_payments.bill_owner_id', auth()->user()->id)->get();

        // dd ($currentuserdata);

        //==========A super admin and ALSO a UNICEF Staff
        if (($hasSuperAdminPermission) &&  (auth()->user()->user_type == 'unicef')) {
            return view('phone.phonelistadmin')->with(['currentuserdata'=> $currentuserdata, 'docOne'=>$docOne,'docTwo'=>$docTwo]);
        }

        //==========NOT super admin BUT a UNICEF Staff
        else  {
            return view('phone.phonelistuser')->with(['currentuserdata'=> $currentuserdata, 'docOne'=>$docOne,'docTwo'=>$docTwo]);
        }

        //==========NOT super admin BUT a UNICEF Staff and bill type is Bank Account, for Status == Reviewed 
        // elseif (((!$hasSuperAdminPermission)) && ((auth()->user()->user_type == 'unicef')) && (!empty($currentuserdata->toArray())) && ($docOne === null) || ($docTwo === null)) {
        //      return view('phone.phonelistreviewed')->with(['currentuserdata'=> $currentuserdata, 'docOne'=>$docOne,'docTwo'=>$docTwo]);
        // }

         //==========NOT super admin BUT a UNICEF Staff and bill type is Bank Account, for Status == Reviewed, Uploaded both doc one and doc Two 
    //      elseif (((!$hasSuperAdminPermission)) && ((auth()->user()->user_type == 'unicef')) && (!empty($currentuserdata->toArray())) && (!empty($docOne)) || (!empty($docTwo))) {
    //         return view('phone.phonelistuser')->with(['docTwo'=>$docOne,'docTwo'=>$docTwo ]);
    //    }

      
    }




    public function identifyCall(Request $request, $phoneBillId)
    {
        return view('phone.callogidentification', compact('phoneBillId'));
    }


    public function downloadFile(Request $request)
    {
        return response()->download(storage_path("/app/public/{$request->file_path}"));
    }
}
