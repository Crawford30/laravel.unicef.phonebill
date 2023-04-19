<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Extension;
use App\CallLogStatus;
use GuzzleHttp\Client;
use Mockery\Undefined;
use App\CallLogPayment;
use App\CallLogTimeline;
use App\Man3000Extension;
use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\IdentifiedUserPhoneBill;
use App\Http\Controllers\Controller;
use App\PhoneBillImportFileDataTemp;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SendLogsToStaffRequest;
use App\Http\Requests\UpdatePhoneBillRequest;
use App\Http\Requests\ReconcilePaymentRequest;
use App\Http\Requests\ImportExcelPhoneBillRequest;
use App\Http\Requests\UploadPaymentReceiptRequest;
use App\Http\Requests\AllIdentifiedPhoneBillRequest;
use App\Http\Requests\PhoneBillReconciliationRequest;
use App\Http\Requests\UpdatePhoneBillReviewedRequest;
use App\Http\Requests\UploadPaymentReceiptTwoRequest;
use App\Http\Requests\UpdatePhoneBillExtensionsRequest;
use App\Http\Requests\ImportPhoneBillWithTemplateRequest;
use App\Http\Requests\UpdatePhoneBillReconciliationRequest;
use App\Http\Requests\CallLogIdentificationCompletedRequest;

class PhoneBillController extends Controller
{




    public function getCurrentUserPhoneBill(Request $request)
    {


         // $currentuserdata =   Man3000Extension::
        // Join('phone_bill_user_data', 'phone_bill_user_data.id', '=', 'man3000_extensions.user_data_id')
        // ->Join('call_log_payments', 'call_log_payments.user_data_id', '=', 'phone_bill_user_data.id')
        // ->where('man3000_extensions.area_code', $singleStaffExten['area_code'])
        // ->orderBy('man3000_extensions.created_at', 'DESC')->get();

        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = phoneBillExtensions::where("area_code",   $singleStaffExten['area_code'])->get();
            $currentuserdata = Man3000Extension::leftJoin('phone_bill_user_data', 'phone_bill_user_data.id', '=', 'man3000_extensions.user_data_id')
                                                ->where('man3000_extensions.area_code', $singleStaffExten['area_code'])
                                                ->orderBy('man3000_extensions.created_at', 'DESC')
                                                ->get();


            // $currentUserData = Man3000Extension::query()->with('phoneBillUserData')
            //                     ->where('man3000_extensions.area_code', $singleStaffExten['area_code'])
            //                     ->orderBy('man3000_extensions.created_at', 'DESC')
            //                     ->get();

            return response()->json($currentuserdata, 200);
        }
    }


    public function getAllUserPhoneBill(Request $request)
    {
        $allPhoneBills = PhoneBillUserData::all()->sortByDesc('created_at');
        return response()->json($allPhoneBills, 200);
    }



    public function getPhoneBillDetails(Request $request, $phoneBillId)
    {

        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $phoneBillDetails = PhoneBillExtensions::where('user_data_id', $phoneBillId)
                ->where("area_code",   $singleStaffExten['area_code'])
                ->orderBy('created_at', 'DESC')->get();
        }

        return  response()->json($phoneBillDetails, 200);
    }

    public function getSingleIdentifiedPhoneBillDetails(Request $request, $phoneBillId)
    {
        $callLogDetails = CallLogPayment::where('id', $phoneBillId)->get();

        $bill_owner_id =  $callLogDetails->pluck('bill_owner_id')->first();
        $user_data_id =  $callLogDetails->pluck('user_data_id')->first();

        $identified_amount =  $callLogDetails->pluck('identified_amount')->first();

        $phoneBillIdentifiedDetails = PhoneBillExtensions::where('bill_owner_id',  $bill_owner_id)->where('user_data_id', $user_data_id)->get();
        $user_data = User::where('id',  $bill_owner_id)->get();

        $user_name = $user_data->pluck('name')->first();

        $callLogStatus =   CallLogStatus::where('user_data_id', $user_data_id)->where('bill_owner_id', $bill_owner_id)->get()->pluck('call_log_with');

        //response()->json($phoneBillIdentifiedDetails);

        return response()->json([
            "submitted_amount" =>  $identified_amount,
            "user_name" => $user_name,
            "phone_bill_details" => $phoneBillIdentifiedDetails,
            "status" => $callLogStatus[0],
        ], 200);
    }




    public function getAllIdentifiedPhoneBill(AllIdentifiedPhoneBillRequest $request)
    {
        return $request->getAllUserIdentifiedCalls($request);
    }


    public function importWithTemplate(ImportPhoneBillWithTemplateRequest $request)
    {
        return $request->import($request);
    }




    public function sendEmail(SendLogsToStaffRequest $request)
    {
        return $request->dispatchEmail($request);
    }



    public function callLogIdentificationCompleted(CallLogIdentificationCompletedRequest $request)
    {
        return $request->identificationComplete($request);
    }


    public function updatePhoneBillDataReviewed(UpdatePhoneBillReviewedRequest $request)
    {
        return $request->updatePhoneBillReviewed($request);
    }


    public function updatePhoneExtensions(UpdatePhoneBillExtensionsRequest $request)
    {
        return $request->updatePhoneBillExtension($request);
    }



    public function phoneBillExtensionsDetails(Request $request, $id)
    {
        //$phonebillextensions = PhoneBillExtensions::with('ext', 'phone_number', 'date_time', 'duration', 'bill_owner_id')->findOrFail($id);

        $phonebillextensions = PhoneBillExtensions::where('bill_owner_id', auth()->user()->id)->where('user_data_id', $id)->select('bill_owner_id', 'ext', 'phone_number', 'date_time', 'duration', 'cost', 'call_type', 'id')->get();

        return response()->json($phonebillextensions, 200);
    }


    public function userDetails(Request $request, $id)
    {

        //  $user_details = PhoneBillExtensions::where('bill_owner_id', $id)->select('ext')->distinct()->get();
        //  $ext  =  $user_details->pluck('ext')->first();

        $user_details = User::where('id', $id)->select('id', 'name', 'email')->get();
        return response()->json($user_details, 200);
    }


    public function uploadPayment(UploadPaymentReceiptRequest $request)
    {
        return $request->uploadReciept($request);
    }

    public function uploadPaymentTwo(UploadPaymentReceiptTwoRequest $request)
    {
        return $request->uploadRecieptTwo($request);
    }

    public function getPhoneBillBSC(PhoneBillReconciliationRequest $request)
    {
        return $request->getPhoneBillBSC($request);
    }



    public function getCallLogTimeline(Request $request, $id)
    {
        $timeline = CallLogTimeline::where('call_log_payment_id', $id)->orderBy('created_at', 'DESC')->get();
        return response()->json($timeline, 200);
    }


    public function updatePayrollReconcilled(UpdatePhoneBillReconciliationRequest $request)
    {
        return $request->updatePhoneBillReconcilled($request);
    }

    public function updatePaymentReceiptReconcilled(ReconcilePaymentRequest $request)
    {
        return $request->updatePhoneBillPaymentReconcilled($request);
    }




    public function downloadUploadedReceipt(Request $request)
    {
        $filePath = Storage::disk('public')->get($request->fileName);
        if(explode(".",$request->fileName)[1] == "pdf"){
            return (new Response($filePath, 200))->header('Content-Type', 'application/pdf');
        }

		return (new Response($filePath, 200))->header('Content-Type', 'image/jpeg');
    }



    public function getPhoneBillFile(Request $request)
    {
        $directory = 'storage/';
        $link = $request->input('link');

        $paths = explode('/', $link);
        $filename = $paths[count($paths) - 1];

        try {
            $request = new Client();
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            $response = $request->request('GET', $link, [
                'headers' => ['Content-Type' => 'application/pdf'],
                'sink' => $directory . $filename
            ]);

            return response()->json('/' . $directory . $filename);
        } catch (\Exception $exception) {
            return response()->json("");
        }
    }



    public function getUserPhoneBillStatus(Request $request, $billownerid)
    {
        $user = request()->user();
        $billownerid = $billownerid ? $billownerid : $user->id;
        $user_call_log_status = CallLogPayment::where('bill_owner_id', $billownerid)->select('id', 'bill_owner_id', 'status', 'personal_calls_count', 'official_calls_count', 'user_data_id')->get();
        return response()->json($user_call_log_status, 200);
    }
}