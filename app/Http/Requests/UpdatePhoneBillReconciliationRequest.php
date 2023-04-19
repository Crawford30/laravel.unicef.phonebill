<?php

namespace App\Http\Requests;

use App\User;
use App\EmailLog;
use App\StaffProfile;
use App\CallLogStatus;
use App\CallLogTimeline;
use App\UserStaff;
use App\UserPermission;
use App\CallLogPayment;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayrollReconcilledBSCEmail;
use App\Mail\PayrollReconcilledAdminEmail;
use App\Mail\PayrollReconcilledOwnerEmail;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhoneBillReconciliationRequest extends FormRequest
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
            "id" => "required",
            "reconcilled_amount" => "required",
            'bsc_email' => "required",
            'bill_owner_id' => "required",
            'user_data_id' =>  "required",
        ];
    }


    public function updatePhoneBillReconcilled($request)
    {


        $user = User::where('id', $request->bill_owner_id)->select('name', 'email')->get();
        $call_log_for_email =  $user->first()->email;

        $call_log_time_payment_updated = CallLogPayment::findOrFail($request->id);




        $call_log_data = [
            "invoice_status" => "Settled",
        ];

        $call_log_time_payment_updated->update($call_log_data);


        $call_time_line_data = [
            "payment_amount" => $request->reconcilled_amount,
            "payment_by" => $request->user()->name,
            "payment_notification" => "Payroll Payment Made",

        ];



        $call_log_time_line_updated = CallLogTimeline::findOrFail($request->id);
        $call_log_time_line_updated->update($call_time_line_data);


        $callLogStatus =   CallLogStatus::updateOrCreate(['user_data_id' => $request->user_data_id, 'bill_owner_id' => $request->bill_owner_id], [
            "call_log_with"  => "Reconciled",
        ]);



        $staffEmailsAndNames = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->get()->makeHidden(['permissions', 'admin']); //admin


        //========Sent to ALL UNICEF Admin======


        $userPermissions = UserPermission::select('user_id', 'permission')->where('permission', 'admin')->get();
        foreach ($userPermissions as $userperm) {
            $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
            foreach ($userStaffs  as $key => $userStaff) {
                $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
                foreach ($staffProfiles as $key => $stafProfile) {

                    //========Sent to ALL UNICEF Admin======
                    $this->dispatchPayrollReconcilledEmailToAdmin($request->call_log_for_name, $stafProfile->name, $stafProfile->email,  $request->reconcilled_amount, date('d/M/Y', strtotime($request->from_date)), date('d/M/Y', strtotime($request->to_date)));
                }
            }
        }




        $this->dispatchPayrollReconcilledEmailToBSC($request->call_log_for_name, $request->bsc_email,  $request->reconcilled_amount,date('d/M/Y', strtotime($request->from_date)), date('d/M/Y', strtotime($request->to_date)));

        $this->dispatchPayrollReconcilledEmailToOwner($request->call_log_for_name, $call_log_for_email,  $request->reconcilled_amount, date('d/M/Y', strtotime($request->from_date)), date('d/M/Y', strtotime($request->to_date)));
    }



    private function dispatchPayrollReconcilledEmailToAdmin($forName, $toName, $email, $reconcilledAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reconciled"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new PayrollReconcilledAdminEmail($emailLog, $forName, $toName, $email, $reconcilledAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchPayrollReconcilledEmailToBSC($forName, $email, $reconcilledAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reconciled"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new PayrollReconcilledBSCEmail($emailLog, $forName, $email, $reconcilledAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }



    private function dispatchPayrollReconcilledEmailToOwner($name, $email, $reconcilledAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reconciled from Payroll"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new PayrollReconcilledOwnerEmail($emailLog, $name, $email, $reconcilledAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}
