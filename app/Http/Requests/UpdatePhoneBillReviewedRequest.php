<?php

namespace App\Http\Requests;

use App\User;
use App\EmailLog;
use App\Extension;
use App\UserStaff;
use App\StaffProfile;
use App\CallLogStatus;
use App\UserFormsData;
use App\CallLogPayment;
use App\UserPermission;
use App\CallLogTimeline;
use App\Man3000Extension;
use App\PhoneBillUserData;
use App\IdentifiedUserPhoneBill;
use App\Mail\AmountExactlyZeroEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExactlyZeroReviewerEmail;
use App\Mail\EqualNotZeroReviewerEmail;
use App\Mail\NotEqualAmountPayrollEmail;
use App\Mail\IdenticalAmountReviewedEmail;
use App\Mail\NotEqualNotZeroReviewerEmail;
use Illuminate\Foundation\Http\FormRequest;
use App\Mail\AmountIdenticalAndNotZeroEmail;
use App\Mail\ExactlyZeroAmountReviewedEmail;
use App\Mail\NotIdenticalAmountPayrollEmail;
use App\Mail\NotIdenticalAmountReviewedEmail;
use App\Mail\AmountGreaterThanZeroPayrollEmail;
use App\Mail\AmountNotIdenticalAndNotZeroEmail;
use App\Mail\IdenticalAmountReviewedPayrollEmail;
use App\Mail\NotEqualAmountPayToUnicefAccountEmail;
use App\Mail\IdenticalAmountPayToUnicefAccountEmail;
use App\Mail\IdenticalAmountReviewedBankAccountEmail;
use App\Mail\ExactlyZeroAmountPayToUnicefAccountEmail;
use App\Mail\AmountGreaterThanZeroSelectedBankBSCEmail;
use App\Mail\AmountGreaterThanZeroSelectedPayrollBSCEmail;

class UpdatePhoneBillReviewedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return in_array('admin', auth()->user()->permissions);

        if (isUserAuthorized("admin", "", false) == true) {
            //grant access to  admin
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            // "submitted_amount" => "required",
            "reviewed_amount" => "required"
        ];
    }


    public function updatePhoneBillReviewed($request)
    {




        $callDetails = CallLogPayment::where('id', (int)$request->phone_bill_id)->select('to_date', 'from_date', 'bill_type', 'call_log_for', 'bill_owner_id', 'identified_amount', 'user_data_id')->get();
        $to_date =  $callDetails->first()->to_date;
        $from_date =  $callDetails->first()->from_date;
        $payment_method = $callDetails->first()->bill_type;
        $from_name =   $callDetails->first()->call_log_for;
        $submitted_amount =  $callDetails->first()->identified_amount;
        $user_data_id = $callDetails->first()->user_data_id;
        $user_bill_owner_id =   $callDetails->first()->bill_owner_id;

        $from_email_array =  User::where('id',  $user_bill_owner_id)->select('email')->where('email', '!=', null)->get();
        $from_email = $from_email_array->first()->email;

        $reviewed_amount = (float) round($request->reviewed_amount, 2);

        

       

      
        $bsc_email = $request->bsc_email;
        $phone_bill_id = (int)$request->phone_bill_id;






        //=======Update the database============
        $data = [
            "reviewed_by" => $request->user()->name,
            "status" =>  "Reviewed",
            "reviewed_amount" =>  (float) round($request->reviewed_amount, 2),
            "invoice_status" => "NotSettled",
            // "invoice_status" => (float) round($request->reviewed_amount, 2) === 0.00 ? "Settled" : "NotSettled", //to automatically Reconcilled for 0 Cost

           
        ];


        $user_phone_bill_updated = CallLogPayment::findOrFail($phone_bill_id);
        $user_phone_bill_updated->update($data);
        // return $user_phone_bill_updated->reviewed_amount;



        $call_log_time_line_updated = CallLogTimeline::findOrFail($phone_bill_id);

        $call_timeline_data = [
            "date_reviewed" => date("d/M/Y", strtotime($user_phone_bill_updated->updated_at)),
            // "reviewed_by" => $request->user()->name,

        ];



        $callLogStatus =   CallLogStatus::updateOrCreate(['user_data_id' => $user_data_id, 'bill_owner_id' => $user_bill_owner_id], [
            "user_data_id" => $user_data_id,
            "bill_owner_id" =>  $user_bill_owner_id,
            "call_log_with"  => "Reviewed",

        ]);


        //return $reviewed_amount;


        // $allStaffExtenstions = Extension::whereNotNull("email")->whereNotNull("ext")->where('email', auth()->user()->email)->get()->flatten();
        // foreach ($allStaffExtenstions as $key => $singleStaffExten) {
        //     $userExtensions = Man3000Extension::where("ext",   $singleStaffExten['ext'])->get();
        // }


        $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name',  $from_name)->get()->flatten();

        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $userExtensions = Man3000Extension::where("area_code",   $singleStaffExten['area_code'])->get();

            $man3000 =   Man3000Extension::updateOrCreate(['area_code' => $singleStaffExten['area_code'], 'user_data_id' => $user_data_id],  [
                "reviewed_amount" =>  $reviewed_amount,

            ]);
        }

        $call_log_time_line_updated->update($call_timeline_data);









        //===========ALL UNICEF ADMIN======
        // $staffEmailsAndNames = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->get()->makeHidden(['permissions', 'admin']); //admin

        $userPermissions = UserPermission::select('user_id', 'permission')->where('permission', 'admin')->get();

        if ($reviewed_amount !== $submitted_amount && $reviewed_amount > 0.00) {

            //========Sent to ALL UNICEF Admin======
            // foreach ($staffEmailsAndNames  as $key => $staffValue) {
            //     $this->dispatchNotEqualNotZeroEmail($staffValue->name, $request->user()->name, $from_name, $staffValue->email,  $submitted_amount, $reviewed_amount, $from_date, $to_date);
            // }

            foreach ($userPermissions as $userperm) {
                $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
                foreach ($userStaffs  as $key => $userStaff) {
                    $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
                    foreach ($staffProfiles as $key => $stafProfile) {
                        //========Sent to ALL UNICEF Admin======
                        $this->dispatchNotEqualNotZeroEmail($stafProfile->name, $request->user()->name, $from_name, $stafProfile->email,  $submitted_amount, $reviewed_amount, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
                    }
                }
            }

            //========Sent to reviewer
            $this->dispatchNotEqualNotZeroReviewerEmail($request->user()->name, $from_name, $request->user()->email, $submitted_amount, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }



        //=========================Reviewed Amount Identical to Submitted Amount
        if ($reviewed_amount === $submitted_amount && $reviewed_amount > 0.00) {

            //=====Sent to ALL UNICEF admin=====
            // foreach ($staffEmailsAndNames  as $key => $staffValue) {
            //     $this->dispatchAmountIdenticalAndNotZeroEmail($staffValue->name, $request->user()->name, $from_name, $staffValue->email,  $reviewed_amount, $from_date, $to_date);
            // }

            foreach ($userPermissions as $userperm) {
                $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
                foreach ($userStaffs  as $key => $userStaff) {
                    $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
                    foreach ($staffProfiles as $key => $stafProfile) {

                        //========Sent to ALL UNICEF Admin======
                        $this->dispatchAmountIdenticalAndNotZeroEmail($stafProfile->name, $request->user()->name, $from_name, $stafProfile->email,  $reviewed_amount, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
                    }
                }
            }
            //======Sent to Reviewer=====
            $this->dispatchEqualAndNotZeroReviewerEmail($request->user()->name, $from_name, $request->user()->email,  $reviewed_amount, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }






        //=======Reviewed AMOUNT 0 ===
        // if ($reviewed_amount ===  0.0) {
        //     foreach ($userPermissions as $userperm) {
        //         $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
        //         foreach ($userStaffs  as $key => $userStaff) {
        //             $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
        //             foreach ($staffProfiles as $key => $stafProfile) {

        //                 $this->dispatchAmountExactlyZeroEmail($stafProfile->name, $request->user()->name, $from_name, $stafProfile->email,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        //             }
        //         }
        //     }

        //     $this->dispatchExactlyZeroReviewerEmail($request->user()->name, $from_name, $request->user()->email, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        // }


        //=======Exactly Zero Reviewed======
        if (($user_phone_bill_updated->reviewed_amount === 0.00)) {
            $this->dispatchExactlyZeroAmountReviewedEmail($from_name, $from_email,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
            foreach ($userPermissions as $userperm) {
                $userStaffs = UserStaff::select('user_id', 'staff_profile_id', 'personal_id')->where('user_id', $userperm->user_id)->get();
                foreach ($userStaffs  as $key => $userStaff) {
                    $staffProfiles = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('id', $userStaff->staff_profile_id)->get();
                    foreach ($staffProfiles as $key => $stafProfile) {
                        //========Sent to ALL UNICEF Admin======
                        $this->dispatchAmountExactlyZeroEmail($stafProfile->name, $request->user()->name, $from_name, $stafProfile->email,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
                    }
                }
            }
            //======Sent to Reviewer====
            $this->dispatchExactlyZeroReviewerEmail($request->user()->name, $from_name, $request->user()->email, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }



       
        //=========Bank Account =========
        if (($reviewed_amount !== $submitted_amount) && ($reviewed_amount > 0.00) && ($payment_method === "UNICEF BANK ACCOUNT")) {
            $this->dispatchNotEqualBankAccountEmail($from_name, $from_email, $submitted_amount, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }

        if (($reviewed_amount === $submitted_amount) && ($reviewed_amount > 0.00) && ($payment_method === "UNICEF BANK ACCOUNT")) {
            $this->dispatchIdenticalAmountPayToBankAccountEmail($from_name, $from_email, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }


        //=======User Selected payroll=======
        if (($reviewed_amount > 0.00) && ($payment_method === "Debit from Payroll") && ($reviewed_amount !== $submitted_amount)) {
            $this->dispatchAmountGreaterThanZeroPayrollEmail($from_name, $from_email, $submitted_amount, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }

        //========Not Identical Reviewed=====
        if (($reviewed_amount !== $submitted_amount) && ($reviewed_amount > 0.00)) {
            $this->dispatchNotIdenticalAmountReviewedEmail($from_name, $from_email, $submitted_amount, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }


        //=====Identical Reviewed Bank Account======
        if (($reviewed_amount === $submitted_amount) && ($reviewed_amount > 0.00) && ($payment_method === "UNICEF BANK ACCOUNT")) {
            $this->dispatchIdenticalAmountReviewedBankAccountEmail($from_name, $from_email, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }
        
        if(($reviewed_amount === $submitted_amount) && ($reviewed_amount > 0.00) && ($payment_method === "Debit from Payroll")) {
              //=====Identical Reviewed Payroll======

              //$username, $email, $identifiedAmount, $fromDate, $toDate
            $this->dispatchIdenticalAmountReviewedPayrollEmail($from_name, $from_email, $reviewed_amount,  date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        }


        //======Amount Greater Zero and Selected Pay to Bank BSC email======
        if (($reviewed_amount > 0.00) && ($payment_method === "UNICEF BANK ACCOUNT")) {
            $this->dispatchAmountGreaterZeroBankBSCEmail(date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)), $from_name, $reviewed_amount, $bsc_email, $phone_bill_id);
        }


        // Amount Greater Zero and Selected Pay to Bank Payroll email
        if (($reviewed_amount > 0.00) && ($payment_method === "Debit from Payroll")) {
            $this->dispatchAmountGreaterZeroBankPayrollEmail(date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)), $from_name, $reviewed_amount, $bsc_email, $phone_bill_id);
        }




        return response()->json($user_phone_bill_updated, 200);
    }






    private function dispatchNotEqualNotZeroEmail($username,  $reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountNotIdenticalAndNotZeroEmail($emailLog, $username,  $reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }





    private function dispatchAmountIdenticalAndNotZeroEmail($username,  $reviewedByName, $whoseCallReviewedName, $email, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountIdenticalAndNotZeroEmail($emailLog, $username,  $reviewedByName, $whoseCallReviewedName, $email, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchAmountExactlyZeroEmail($username,  $reviewedByName, $whoseCallReviewedName, $email, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountExactlyZeroEmail($emailLog, $username,  $reviewedByName, $whoseCallReviewedName, $email, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchNotEqualNotZeroReviewerEmail($reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new NotEqualNotZeroReviewerEmail($emailLog, $reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchEqualAndNotZeroReviewerEmail($reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new EqualNotZeroReviewerEmail($emailLog, $reviewedByName, $whoseCallReviewedName, $email, $submittedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }



    private function dispatchExactlyZeroReviewerEmail($reviewedByName, $whoseCallReviewedName, $email, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new ExactlyZeroReviewerEmail($emailLog,  $reviewedByName, $whoseCallReviewedName, $email, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchNotEqualBankAccountEmail($username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();
        try {
            $mail = (new NotEqualAmountPayToUnicefAccountEmail($emailLog, $username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchIdenticalAmountPayToBankAccountEmail($username, $email, $submittedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();
        try {
            $mail = (new IdenticalAmountPayToUnicefAccountEmail($request->phone_bill_id, $emailLog, $username, $email, $submittedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {}
    }


    private function dispatchExactlyZeroAmountReviewedEmail($username, $email, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new ExactlyZeroAmountReviewedEmail($emailLog, $username, $email, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }




    private function dispatchAmountGreaterThanZeroPayrollEmail($username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountGreaterThanZeroPayrollEmail($emailLog, $username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }




    private function dispatchNotIdenticalAmountReviewedEmail($username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();
        try {
            $mail = (new NotIdenticalAmountReviewedEmail($emailLog, $username, $email, $submittedAmount, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }



    private function dispatchIdenticalAmountReviewedBankAccountEmail($username, $email, $identifiedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new IdenticalAmountReviewedBankAccountEmail($emailLog, $username, $email, $identifiedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    //$emailLog, $username, $email,$identifiedAmount, $fromDate, $toDate

    private function dispatchIdenticalAmountReviewedPayrollEmail($username, $email, $identifiedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Reviewed by Administration"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new IdenticalAmountReviewedPayrollEmail($emailLog, $username, $email, $identifiedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    



    private function dispatchAmountGreaterZeroBankBSCEmail($fromDate, $toDate, $fromName, $reviewedAmount, $email, $phoneBillId)
    {

        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill for Reconciliation"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountGreaterThanZeroSelectedBankBSCEmail($emailLog, $fromDate, $toDate, $fromName, $reviewedAmount, $email, $phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }



    private function dispatchAmountGreaterZeroBankPayrollEmail($fromDate, $toDate, $fromName, $reviewedAmount, $email, $phoneBillId)
    {

        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill for Reconciliation"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new AmountGreaterThanZeroSelectedPayrollBSCEmail($emailLog, $fromDate, $toDate, $fromName, $reviewedAmount, $email, $phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}