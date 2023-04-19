<?php

use App\User;
use App\EmailLog;
use Carbon\Carbon;
use App\DollarRate;
use App\Leave\Leave;
use App\StaffProfile;

use GuzzleHttp\Client;
use App\Leave\LeaveDelegation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response as ResponseAlias;

function apiResponse($results, $status = ResponseAlias::HTTP_OK)
{
    return response()->json([
        "results" => $results,
    ], $status);
}

function apiErrorResponse($message, $errors = [], $status = ResponseAlias::HTTP_BAD_REQUEST)
{
    return response()->json([
        "message" => $message,
        "errors" => $errors
    ], $status);
}

function isLinkActive(array $links, $class = "active")
{
    foreach ($links as $link) {
        if (Request::is($links)) {
            return $class;
        }
    }
    return "";
}

function generateRandomString($length = 6)
{
    $alphabet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $id = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $p = mt_rand(0, $alphaLength);
        $id[] = $alphabet[$p];
    }
    return implode($id);
}

function generateRandomNumber($length = 6)
{
    $alphabet = '123456789';
    $id = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $p = mt_rand(0, $alphaLength);
        $id[] = $alphabet[$p];
    }
    return implode($id);
}

function storeFile($path, $file)
{
    $filen = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
    $filename = $path . $filen . '.' . $file->getClientOriginalExtension();
    Storage::disk('public')->put($filename, File::get($file));
    return (object)["filename" => $file->getClientOriginalName(), "file_path" => $filename];
}

function formatDate($date, $format = "d/M/y")
{
    try {
        $carbonDate = \Carbon\Carbon::parse($date);
        return $carbonDate->format($format);
    } catch (Exception $exception) {
        return $date;
    }
}

function randomColor()
{
    $colors = [
        '#A3A1FB', '#DDDF00', '#24CBE5', '#64E572', '#FF9655',
        '#9DFCFF', '#FFD59D', '#FC46FC', '#00E5E5', '#E500B9',
        '#6CC476', '#E59F00'
    ];

    return $colors[array_rand($colors)];
}



/*
* Method to globalize the authentication of a user
* @parameters: $permission - examples of permissions are f_tat, c_bsc etc
* @parameters: $position - examples of positions are "Finance Officer", "ICT Specialist" etc. Since some sections in UNICEF share the same positions/titles, it is always advisable to use the $position and $section side by side
* @parameters: $section - examples of sections are WASH, Nutrition etc
* @parameters: $strict - if strict is set to 'Yes', then authenticate only the permission or section i.e a Super Admin cannot access the feature
* Usage 1: check if the user has the specified permissions and note that super admins can still access this feature
* method may be used as follows:
* if(isUserAuthorized("f_tat","","","false")=="true") {
*  //grant access or display a feature e.g a menu if the logged in user has f_tat or s_admin permissions
* }
* Usage 2: check if the user has the specified permissions, strictly i.e super admins cannot access this feature
* method may be used as follows:
* if(isUserAuthorized("f_tat","","","true")=="true") {
*  //grant access or display a feature e.g a menu if the logged in user has f_tat (a super admin cannot access this feature, only a user with f_tat permissions)
* }
* Usage 3: check if the user is from a specific section
* method may be used as follows:
* if(isUserAuthorized("","","WASH","false")=="true") {
*  //grant access or display a feature e.g a menu if the logged in user is from the WASH section or has s_admin permissions
* }
*
* Usage 4: check if the user holds the specified position from the given section
* method may be used as follows:
* if(isUserAuthorized("","Administrative Officer","WASH","false")=="true") {
*  //grant access or display a feature e.g a menu if the logged in user holds the position of "Administrative Officer" from the "WASH" section or has s_admin permissions etc
* }
*/

function isUserAuthorized($permission, $position, $section, $strict = false)
{
    //get the user
    $user = auth()->user();
    //get the user's section
    $userSection = auth()->user()->section ? auth()->user()->section : null;
    //get the user's position
    $userPosition = auth()->user()->profile->position_text ? auth()->user()->profile->position_text  : null;

    //isAuthorized true or false
    $isAuthorized = false;

    //Usage 1 || 2: check if $permission has been specified
    if ($permission != null) {
        //if we are not being strict about the permissions, then proceed
        if (strtolower($strict) == false) {
            //does the current logged in user have the required permissions or is the user a super admin?
            if ($user !== null && (collect($user->permissions)->contains($permission) || collect($user->permissions)->contains("s_admin"))) {
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user with the required permissions
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($user)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                if ($supervisor !== null && (collect($supervisor->permissions)->contains($permission) || collect($supervisor->permissions)->contains("s_admin"))) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        } else if (strtolower($strict) == "true") {
            /*
            * strict enforcement of permissions i.e only users with the stated permissions should be given access
            * does the current logged in user have the required permissions
            */
            if ($user !== null && collect($user->permissions)->contains($permission)) {
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user with the required permissions
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($user)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                if ($supervisor !== null && collect($supervisor->permissions)->contains($permission)) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        }
        //usage 3: check if only the $section has both been specified
    } else if ($position == null && $section != null) {
        //if we are not being strict about the section, then proceed
        if (strtolower($strict) == "false") {
            //check if $section has been specified (let's avoid case sensitivity), or is the user a super admin
            if (strtolower($userSection) == strtolower($section) || collect($user->permissions)->contains("s_admin")) {
                //does the current logged in user belong to the required section
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user within the required section
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($userSection)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                //check if the supervisor both bears the requisite position and belongs to the authenticated section or is a super admin
                if (strtolower($supervisor->section) == strtolower($section) || collect($supervisor->permissions)->contains("s_admin")) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        } else if (strtolower($strict) == "true") {
            /*
            * strict enforcement of permissions i.e only users with the stated permissions should be given access
            * does the current logged in user have the required permissions
            */
            //check if both $position &&  $section have been specified - let's avoid case sensitivity
            if (strtolower($userSection) == strtolower($section)) {
                //does the current logged in user belong to the required section
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user within the required section
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($userSection)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                //check if the supervisor both bears the requisite position and belongs to the authenticated section
                if (strtolower($supervisor->section) == strtolower($section)) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        }
        //usage 4: check if both $position and $section have both been specified
    } else if ($position != null && $section != null) {
        //if we are not being strict about the section, then proceed
        if (strtolower($strict) == "false") {
            //check if $section has been specified (let's avoid case sensitivity), or is the user a super admin
            if ((strtolower($userPosition) == strtolower($position) && strtolower($userSection) == strtolower($section)) || collect($user->permissions)->contains("s_admin")) {
                //does the current logged in user belong to the required section
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user within the required section
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($userSection)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                //check if the supervisor both bears the requisite position and belongs to the authenticated section or is a super admin
                if ((strtolower($supervisor->position) == strtolower($position) && strtolower($supervisor->section) == strtolower($section)) || collect($supervisor->permissions)->contains("s_admin")) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        } else if (strtolower($strict) == "true") {
            /*
            * strict enforcement of permissions i.e only users with the stated permissions should be given access
            * does the current logged in user have the required permissions
            */
            //check if both $position &&  $section have been specified - let's avoid case sensitivity
            if (strtolower($userPosition) == strtolower($position) && strtolower($userSection) == strtolower($section)) {
                //does the current logged in user belong to the required section
                $isAuthorized =  true;
            } else if ($user !== null && isDeputized($user)) {
                /*
                * is the current logged in user the OIC (Officer in Charge - this means they are deputizing someone who has gone on leave)
                * of another user within the required section
                */
                $supervisorDetails = StaffProfile::query()->where('personal_id', getUsersSupervisor($userSection)->personal_id)->first();
                $supervisor = User::query()->where('email', $supervisorDetails->email)->first();
                //check if the supervisor both bears the requisite position and belongs to the authenticated section
                if (strtolower($supervisor->position) == strtolower($position) && strtolower($supervisor->section) == strtolower($section)) {
                    //when does the leave (of the supervisor) begin
                    $startDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->start);
                    //when does the leave (of the supervisor) end
                    $endDate = Carbon::createFromFormat('Y-m-d', getUsersSupervisor($user)->end);
                    //if the current date lies between the leave days
                    if (Carbon::now()->between($startDate, $endDate)) {
                        $isAuthorized =  true;
                    } else {
                        $isAuthorized = false;
                    }
                } else {
                    $isAuthorized = false;
                }
            } else {
                $isAuthorized = false;
            }
        }
    }
    return $isAuthorized;
}

/*
* If user1 goes on leave and user1 indicates that user2 is their OIC (Officer in Charge - or deputy)
* Then isDeputized(user2) should return true thereby indicating that user2 is the deputy of someone on leave i.e user1
* @parameters: $user, in this case, running isDeputized("user2") checks whether user2 is a deputy or OIC of someone
*/
function isDeputized($user)
{
    $staff = StaffProfile::query()->where('email', $user->email)->first();
    $leaveDelegation = LeaveDelegation::query()->where('delegated_role_to', $staff->personal_id)
        ->orderBy('created_at', 'desc')->first();
    return $leaveDelegation != null ? true : false;
}

/*
* If user1 goes on leave and user1 indicates that user2 is their OIC (Officer in Charge - or deputy)
* Then getUsersSupervisor(user2) should return user1 thereby indicating that user1 is the supervisor of user2 or user2 is the OIC of user1
* @parameters: $user, in this case, running getUsersSupervisor("user2") returns who the supervisor is user2 is.
*/
function getUsersSupervisor($user)
{
    $staff = StaffProfile::query()->where('email', $user->email)->first();
    $leaveDelegation = LeaveDelegation::query()->where('delegated_role_to', $staff->personal_id)
        ->orderBy('created_at', 'desc')->first();
    $supervisor = Leave::query()->where('id', $leaveDelegation->leave_id)
        ->orderBy('created_at', 'desc')->first();
    return $supervisor;
}

/* 
* @params: User $user
*
* This function gets details of the supervisor's officer in charge 
* i.e the staff who has been delegated the roles incase the supervisor is on leave.
* To use this function pass in a parameter which is a User object and it will get the OIC.
*
*/
function getSupervisorsOIC($user)
{
    $staff = StaffProfile::query()->where('email', $user->email)->first();

    $staffsLeaveDetails = Leave::query()->where('personal_id', $staff->personal_id)
        ->orderBy('created_at', 'desc')->first();

    $leaveDelegation = LeaveDelegation::query()->where('leave_id', $staffsLeaveDetails->id)
        ->orderBy('created_at', 'desc')->first();

    $officerInCharge = StaffProfile::query()->where('personal_id', $leaveDelegation->delegated_role_to)->first();

    return $officerInCharge;
}


/*
* Global function to send mail to the recipient and his/her officer in charge (OIC)
* @params: $recipient - the intended recipient of the mail. Must be a User object.
* @params: $subject - the email subject
* @params: $mailable - the email class which inherits the Mailable class
*
*
* For Example
* To send an email to both the recipient and his/her OIC do the following
*
* $mailable = new App\Mail\Staff\HelloWorldMail();
* $recipient = App\User::query()->find($recipientsId);
* $subject = "Hello this is a sample Email";
* 
* Now call the globalSendEmail() and pass the above variables as parameters to the function as shown below.
*
* globalSendEmail($recipient, $subject, $mailable)
*
* NOTE: You can call the above function anywhere in your class e.g controller class, 
* request class or repository class. ENSURE THAT YOU PASS IN ALL REQUIRED PARAMETERS TO THE FUNCTION.
*/
function globalSendEmail(User $recipient, $subject, $mailable)
{
    //Check if the recipient is a UNICEF STAFF staff
    if ($recipient != NULL && $recipient->user_type == 'unicef') {
        $recipientProfile = StaffProfile::query()->where('email', $recipient->email)->first();
        $recipientLeaveDetails = Leave::query()->where('personal_id', $recipientProfile->personal_id)->orderBy('created_at', 'desc')->first();
        if ($recipientLeaveDetails) {
            $leaveStartDate = Carbon::parse($recipientLeaveDetails->start)->format('Y-m-d');
            $leaveEndDate = Carbon::parse($recipientLeaveDetails->end)->format('Y-m-d');
            $isRecipientOnLeave = Carbon::now()->between($leaveStartDate, $leaveEndDate);

            // check if the recipient is still on leave
            if ($isRecipientOnLeave == true) {
                $recipientsOIC = getSupervisorsOIC($recipient); //get the recipient's officer in charge
                $recipientOICEmailLog = EmailLog::create([ // Now send a copy of email to the recipient's OIC
                    "to" => $recipientsOIC->email,
                    "description" => $subject . "[OIC]"
                ]);
                $recipientOICEmailLog->updateCode();

                try {
                    $recipientOICEmailLog->update([
                        'body' => $mailable->render()
                    ]);
                    Mail::to($recipientsOIC->email)->send($mailable);
                } catch (\Exception $e) {
                }
            }
        }
    }

    // send an email to recipient. 
    // The recipient can be a UNICEF staff, service provider (SP) or an implementing partner (IP)
    $recipientsEmailLog = EmailLog::create([
        "to" => $recipient->email,
        "description" => $subject
    ]);

    $recipientsEmailLog->updateCode();

    try {
        $recipientsEmailLog->update([
            'body' => $mailable->render()
        ]);
        Mail::to($recipient->email)->send($mailable);
    } catch (\Exception $e) {
    }
}



// function sendMailToStaffOnLeave($to,$user,$subject,$body,$attachments = null)
// {
//     $senderMail = env('MAIL_FROM_ADDRESS');
//     $staff = StaffProfile::query()->where('email',$user->email)->first();

//     $staffsLeaveDetails = Leave::query()->where('personal_id',$staff->personal_id)
//         ->orderBy('created_at','desc')->first();

//     $startDate = Carbon::createFromFormat('Y-m-d',$staffsLeaveDetails->start);

//     $endDate = Carbon::createFromFormat('Y-m-d',$staffsLeaveDetails->end);

//     if(Carbon::now()->between($startDate,$endDate)){
//         $officerInCharge = getSupervisorsOIC($user);

//         $emailLog = EmailLog::create([
//             "to" => $officerInCharge->email,
//             "description" => $subject
//         ]);

//         $emailLog->updateCode();

//         try {
//             $emailLog->update([
//                 "body" => $body->render()
//             ]);
//             Mail::to($officerInCharge->email)->send($body);
//         } catch (\Exception $exception) {}
//     }
// }