<?php

namespace App\Http\Requests;

use App\EmailLog;
use App\StaffProfile;
use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use App\CallLogPayment;
use App\CallLogStatus;
use App\CallLogTimeline;
use App\UserStaff;
use App\UserPermission;
use App\Section;
use App\Man3000Extension;
use App\Extension;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use App\Mail\IdentificationCompletedStaffEmail;
use App\Mail\IdentificationCompletedAdministrationEmail;

class CallLogIdentificationCompletedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "personal_call_count" => "required",
            "identified_amount" => "required",
            "bill_type" => "required",
            "official_call_count" => "required",
            "total_calls_count" => "required",
            "all_identified_calls" => "required",
            "allowance_amount" => "required"

        ];
    }



    public function identificationComplete($request)
    {

        // $phoneBillExtensions = PhoneBillExtensions::where('bill_owner_id', $request->user()->id)->get();
        $user_data_id =  $request->user_data_id;
        $all_identified_calls = json_decode($request->all_identified_calls, true);


        foreach ($all_identified_calls as $user_bill) {
            $data = [
                "id" =>  $user_bill["id"],
                "bill_owner_id" =>  $request->user()->id,
                "call_type" =>  empty( $user_bill["call_type"]) ? "Personal" :  $user_bill["call_type"],
                "name" => $user_bill["name"],
            ];


            $form = PhoneBillExtensions::updateOrCreate(["id" => $user_bill["id"]], $data);
        }

        $userDataArray = PhoneBillUserData::where('id', $user_data_id)->select('to_date', 'from_date', 'created_at')->get();

        $to_date =  $userDataArray->first()->to_date;
        $from_date =  $userDataArray->first()->from_date;
        $created_at_date =  $userDataArray->first()->created_at;

        $staffUser = UserStaff::where('user_id', $request->user()->id)->select('staff_profile_id')->get()->first();

        $staffProfile = StaffProfile::where('id', $staffUser->staff_profile_id)->select('section')->get()->first();

        $userSection = Section::where('id', $staffProfile->section)->select('name')->get()->first();



        $data = [
            "bill_owner_id" =>  $request->user()->id,
            "user_data_id" =>  $user_data_id,
            "personal_calls_count" => $request->personal_call_count,
            "official_calls_count" => $request->official_call_count,
            "identified_amount" => round($request->identified_amount, 2),
            "bill_type" => $request->bill_type,
            "status" => "With ADMIN",
            "call_log_for" =>  $request->user()->name,
            "section" =>  $userSection->name,
            "from_date" =>  $from_date,
            "to_date" =>   $to_date

        ];


        $callLogPaymentDetails = CallLogPayment::updateOrCreate(['user_data_id' => $user_data_id, 'bill_owner_id' => $request->user()->id], $data);


        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', auth()->user()->name)->get()->flatten();
        
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = Man3000Extension::where("area_code",   $singleStaffExten['area_code'])->get();
           
        }

        // return response()->json($currentuserdata, 200);

        // $allStaffExtenstions = Extension::whereNotNull("email")->whereNotNull("ext")->where('email', auth()->user()->email)->get()->flatten();
        // foreach ($allStaffExtenstions as $key => $singleStaffExten) {
        //     $userExtensions = Man3000Extension::where("ext",   $singleStaffExten['ext'])->get();
        // }


        $man3000 =   Man3000Extension::updateOrCreate(['area_code' => $singleStaffExten['area_code'], 'user_data_id' => $user_data_id],  [
            "identified_amount" => round($request->identified_amount, 2),

        ]);



        $call_timeline_data = [
            "call_log_payment_id" =>  $callLogPaymentDetails->id,
            "date_identified" => date("d/M/Y", strtotime($callLogPaymentDetails->updated_at)),
            "user_data_id" => $user_data_id,
            "date_uploaded" => date("d/M/Y", strtotime($created_at_date)),
            "identified_by" =>  $request->user()->name,
            "identified_by_id" => $callLogPaymentDetails->bill_owner_id
        ];
        $callLogTime = CallLogTimeline::updateOrCreate(['user_data_id' => $user_data_id, 'identified_by_id' => $request->user()->id], $call_timeline_data);



        $callLogStatus =   CallLogStatus::updateOrCreate(['user_data_id' => $user_data_id, 'bill_owner_id' => $request->user()->id], [
            "user_data_id" => $user_data_id,
            "bill_owner_id" =>  $request->user()->id,
            "call_log_with"  => "With ADMIN",
            "personal_count" => $callLogPaymentDetails->personal_calls_count,
            "official_count" => $callLogPaymentDetails->official_calls_count,
            "total_count" => $request->total_calls_count

        ]);

       

        if($request->official_call_count === 0 || empty($request->official_call_count) ){
            $official_percentage = 0;
        }else{
            $official_percentage = round((($request->official_call_count) / ($request->total_calls_count)) * 100, 2);

        }

        if($request->personal_call_count === 0 || empty($request->personal_call_count) ){
            $personal_percentage = 0;
        }else{
            $personal_percentage = round((($request->personal_call_count) / ($request->total_calls_count)) * 100, 2);
        }


       
        //=======Query to get Staff with ADMIN permission =========
        $userPermissions = UserPermission::select('user_id', 'permission')->where('permission', 'admin')->get();
        foreach ($userPermissions as $userperm) {
            $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
            foreach ($userStaffs  as $key => $userStaff) {
                $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
                foreach ($staffProfiles as $key => $stafProfile) {
                    $this->dispatchCompletedAdministrationEmail($request->user()->name,   date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)),  $stafProfile->email, $request->official_call_count, $request->personal_call_count,round($request->identified_amount, 2), $request->bill_type, $official_percentage, $personal_percentage, $stafProfile->name,  $this->allowance_amount, $callLogPaymentDetails->id);
                }
            }
        }


        //Email Sent to staff
        $this->dispatchCompletedStaffEmail($request->user()->name,   date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)),  $request->user()->email, $request->official_call_count, $request->personal_call_count, round($request->identified_amount, 2), $request->bill_type, $official_percentage, $personal_percentage, $this->allowance_amount);


        return response()->json($callLogPaymentDetails, 200);
    }




    private function dispatchCompletedAdministrationEmail($fromName,  $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $toName, $allowanceAmount, $phoneBillId)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Call Log Identification Completed"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new IdentificationCompletedAdministrationEmail($emailLog, $fromName,  $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $toName, $allowanceAmount, $phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchCompletedStaffEmail($username,  $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Call Log Identification Completed"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new IdentificationCompletedStaffEmail($emailLog, $username,  $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}