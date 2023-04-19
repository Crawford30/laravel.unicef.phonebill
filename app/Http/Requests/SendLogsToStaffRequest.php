<?php

namespace App\Http\Requests;

use App\User;
use App\Section;
use App\EmailLog;
use App\UserStaff;
use App\StaffProfile;
use App\UserPermission;
use App\Man3000Extension;
use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use Illuminate\Support\Facades\DB;
use App\Mail\MonthlyCallLogICTEmail;
use App\PhoneBillImportFileDataTemp;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoUserFoundForExtension;
use App\Mail\MonthlyCallLogStaffEmail;
use Illuminate\Foundation\Http\FormRequest;

class SendLogsToStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return in_array('s_admin', auth()->user()->permissions); //super admin

        if (isUserAuthorized("s_admin", "", false) == true) {
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
            // "staff_extensions" => "required"
        ];
    }



    public function dispatchEmail($request)
    {
        $user = User::where('id', 1)->orderBy('created_at', 'DESC')->first();
        $allPhoneBillData = PhoneBillUserData::latest()->take(1)->get();
        $userBillData =  PhoneBillUserData::latest()->select('to_date', 'from_date', 'unique_mobile_number_count', 'total_monthly_cost', 'identification_deadline_date', 'total_records', 'id',)->take(1)->get();
        $userDataInfo = $userBillData->first();
        $uniqueExtensions =  $allPhoneBillData->pluck("extensions")->flatten()->toArray();

        $items = json_decode($request->staff_extensions);
        $staffProfiles = StaffProfile::all();

        foreach ($userBillData  as $key => $allPhonebill) {
            $namesFromExtract = array();
            $namesFromSystem = array();

            $firstNameFromExtract = "";
            $middleNameFromExtract = "";
            $lastNameFromExtract = "";


            $firstNameFromSystem = "";
            $middleNameFromSystem = "";
            $lastNameFromSystem = "";


            $man3000 = Man3000Extension::where("user_data_id", $allPhonebill->id)->get();

            $namePattern = '/[^A-Za-z0-9\-]/';

            foreach ($man3000  as $key => $eachman300) {

                foreach ($staffProfiles  as $key => $staffprofile) {

                    $namesFromSystem =  preg_split('/\s{1,}/',   trim($staffprofile->name));
                    $namesFromExtract = preg_split('/\s{1,}/', trim($eachman300->name));

                    $firstNameFromExtract = strtolower(trim(preg_replace($namePattern, '', $namesFromExtract[array_key_first($namesFromExtract)])));
                    $lastNameFromExtract = strtolower(trim(preg_replace($namePattern, '',  $namesFromExtract[array_key_last($namesFromExtract)])));

                    $firstNameFromSystem = strtolower(trim(preg_replace($namePattern, '', $namesFromSystem[array_key_first($namesFromSystem)])));
                    $lastNameFromSystem =   strtolower(trim(preg_replace($namePattern, '', $namesFromSystem[array_key_last($namesFromSystem)])));

                    if ((($firstNameFromExtract == $firstNameFromSystem)   ||  ($firstNameFromExtract  == $lastNameFromSystem))  && (($lastNameFromExtract  ==  $firstNameFromSystem)    ||  ($lastNameFromExtract  == $lastNameFromSystem))) {
                        if ($staffprofile->email != null) {
                            $result = empty($staffprofile->mobile) ? 'NO PHONE NUMBER FOUND' : $staffprofile->mobile;
                                $this->dispatchMonthlyCallLogStaffEmail($staffprofile->name, $result,  date('d/M/Y', strtotime($allPhonebill->from_date)), date('d/M/Y', strtotime($allPhonebill->to_date)), $eachman300->mobile_number_unique_count, $eachman300->total_monthly_cost, $allPhonebill->identification_deadline_date, $staffprofile->email, $allPhonebill->id);
                        }
                    }
                }
            }
        }


        if ($firstNameFromExtract === "not" || $lastNameFromExtract === "up") {
            $this->dispatchNoUserFoundEmail($user->email, $user->name, "NOT SET UP",  date('d/M/Y', strtotime($allPhonebill->from_date)), date('d/M/Y', strtotime($allPhonebill->to_date)));
        }

        $users =   Section::select('id', 'name')->where('name', 'ICT Unit')->distinct()
            ->get();


        foreach ($users as $key => $user) {
            $iCTStaffEmailsAndNames = StaffProfile::select('email', 'name')->where('email', '!=', null)->where('email', '!=', "")->where('name', '!=', null)->where('name', '!=', "")->where('section', $user->id)->get();
        }

        foreach ($iCTStaffEmailsAndNames  as $key => $iCTStaffValue) {
            $this->dispatchMonthlCallLogICTEmail($iCTStaffValue['name'],  date('d/M/Y', strtotime($userDataInfo->from_date)), date('d/M/Y', strtotime($userDataInfo->to_date)), $userDataInfo->unique_mobile_number_count, $userDataInfo->unique_mobile_number_count,  $userDataInfo->total_records, $iCTStaffValue['email']);
        }

        return response()->json($allPhoneBillData, 200);
    }




    private function dispatchMonthlCallLogICTEmail($username,  $fromDate, $toDate, $totalUniqueMobileNumbersCalled, $totalUniquePhones, $totalRecords, $email)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Monthly Call Log"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new MonthlyCallLogICTEmail($emailLog, $username,  $fromDate, $toDate, $totalUniqueMobileNumbersCalled, $totalUniquePhones, $totalRecords, $email));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }






    private function dispatchNoUserFoundEmail($email, $username, $name,  $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "No Users Found"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new NoUserFoundForExtension($emailLog, $email, $username, $name, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }


    private function dispatchMonthlyCallLogStaffEmail($username, $userphonenumber, $fromDate, $toDate, $totalUniqueNumbersCalled, $totalCost, $identificationDeadline, $email, $phoneBillId)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Monthly Call Log"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new MonthlyCallLogStaffEmail($emailLog, $username, $userphonenumber, $fromDate, $toDate, $totalUniqueNumbersCalled, $totalCost, $identificationDeadline, $email, $phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}