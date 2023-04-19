<?php

namespace App\Console\Commands;

use DateTime;
use App\EmailLog;
use App\Extension;
use Carbon\Carbon;
use App\StaffProfile;
use App\Man3000Extension;
use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnicefEmailReminderToIdentifyCallsEmail;

class PhoneBillIdentificationReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebill:staff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a scheduled reminder to all UNICEF Staff who havent identified thier PhoneBills and the deadline is approaching';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$phoneBillsNotIdentified = PhoneBillExtensions::where('bill_owner_id', null)->unique('ext')->get();

        $staffProfiles = StaffProfile::select('email', 'name')->whereNotNull('email')->whereNotNull('name')->get();
        $man3000Extensions = Man3000Extension::all();

        $phoneBillsNotIdentified = PhoneBillExtensions::select('area_code')->where('bill_owner_id', null)->distinct()->get(); //unique extension

        $phoneBillUserData = PhoneBillUserData::get();
        $allowedDays = [1, 3, 4]; //send reminder when 1, 3, 4 days remaining
        $namePattern = '/[^A-Za-z0-9\-]/';

        foreach ($man3000Extensions as $key => $eachman3000) {
            foreach (($phoneBillsNotIdentified) as $phoneBillNotIdentified) {
                if ($eachman3000['area_code'] != null && $eachman3000['name'] != null) {
                    if ($phoneBillNotIdentified->area_code === $eachman3000['area_code']) {

                        foreach ($phoneBillUserData as $key => $userPhoneBill) {
                            foreach ($staffProfiles as  $staffprofile) {
                                $namesFromSystem =  preg_split('/\s{1,}/',   trim($staffprofile->name));
                                $namesFromExtract = preg_split('/\s{1,}/', trim($eachman3000->name));
                                $firstNameFromExtract = strtolower(trim(preg_replace($namePattern, '', $namesFromExtract[array_key_first($namesFromExtract)])));
                                $lastNameFromExtract = strtolower(trim(preg_replace($namePattern, '',  $namesFromExtract[array_key_last($namesFromExtract)])));

                                $firstNameFromSystem = strtolower(trim(preg_replace($namePattern, '', $namesFromSystem[array_key_first($namesFromSystem)])));
                                $lastNameFromSystem =   strtolower(trim(preg_replace($namePattern, '', $namesFromSystem[array_key_last($namesFromSystem)])));

                                if ((($firstNameFromExtract == $firstNameFromSystem)   ||  ($firstNameFromExtract  == $lastNameFromSystem))  && (($lastNameFromExtract  ==  $firstNameFromSystem)    ||  ($lastNameFromExtract  == $lastNameFromSystem))) {
                                    if ($staffprofile->email != null) {
                                        $dateString = str_replace('/', '-', $userPhoneBill['identification_deadline_date']);
                                        $date = new DateTime($dateString);
                                        $newDate = ($date->format('d-m-Y'));

                                        $daysRemaining =  (int) round((strtotime($newDate) - time())  / (60 * 60 * 24));

                                        //dd(in_array($daysRemaining, $allowedDays));

                                        if (in_array($daysRemaining, $allowedDays)) {

                                            $emailLog = EmailLog::create([
                                                "to" => $staffprofile['email'],
                                                "description" => "Approaching Phone Bill Identification Deadline"
                                            ]);

                                            $emailLog->updateCode();
                                            try {
                                                $mail = new UnicefEmailReminderToIdentifyCallsEmail($emailLog, $staffprofile->name, date('d/M/Y', strtotime($userPhoneBill['from_date'])), date('d/M/Y', strtotime($userPhoneBill['to_date'])), $eachman3000->mobile_number_unique_count, $eachman3000->total_monthly_cost, $userPhoneBill['identification_deadline_date'], $staffprofile['email'], $userPhoneBill['id'], $daysRemaining);
                                                $emailLog->update([
                                                    "body" => $mail->render()
                                                ]);
                                                Mail::to($staffprofile->email)->send($mail);
                                            } catch (\Exception $exception) {
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
