<?php

namespace App\Http\Requests;

use App\EmailLog;
use App\CallLogStatus;
use App\CallLogTimeline;
use App\CallLogPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use App\Mail\UploadedPaymentEvidenceBSCEmail;
use App\Mail\UploadedPaymentEvidenceUserEmail;

class UploadPaymentReceiptTwoRequest extends FormRequest
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
            "document_two" => "required",
            "allowance_amount" => "required",
            "id" => "required",

        ];
    }


    public function uploadRecieptTwo($request)
    {

        if ($request->hasFile('document_two')) {
            $file = storeFile('receipt_two', $request->file('document_two'));
            $imagePath = $file->file_path;
        } else {
            $imagePath = null;
        }


        $data = [
            'id' => $request->id,
            'document_two' => $imagePath,
        ];


        $user_phone_bill_updated =  CallLogPayment::updateOrCreate(["user_data_id" => $request->user_data_id, 'bill_owner_id' => $request->user()->id], $data);

        $getUserData = CallLogPayment::where('id', $request->id)->select('to_date', 'from_date', 'reviewed_amount', 'official_calls_count', 'personal_calls_count')->get();

        $to_date =  $getUserData->first()->to_date;
        $from_date =  $getUserData->first()->from_date;
        $reviewed_amount =  $getUserData->first()->reviewed_amount;
        $personal_count = $getUserData->first()->personal_calls_count;
        $official_count = $getUserData->first()->official_calls_count;

        $total_count = ($personal_count + $official_count);
        // $official_percentage = round((($official_count) / ($total_count)) * 100, 2);
        // $personal_percentage = round((($personal_count) / ($total_count)) * 100, 2);

        if($total_count === 0 ){
            $official_percentage = 0;
            $personal_percentage = 0;
        }else{
            $official_percentage = round((($official_count) / ($total_count)) * 100, 2);
            $personal_percentage = round((($personal_count) / ($total_count)) * 100, 2);

        }



        $call_timeline_data = [
            "payment_amount" => $reviewed_amount,
            "payment_by" => $request->user()->name,
            "payment_notification" => "Payment Receipt Uploaded",

        ];

        CallLogTimeline::updateOrCreate(["user_data_id" => $request->user_data_id, "identified_by_id" => $request->user()->id], $call_timeline_data);

        CallLogStatus::updateOrCreate(["user_data_id"  => $request->user_data_id, "bill_owner_id" => $request->user()->id], ["cal_log_with" => "Payment Evidence Sent"]);




        $this->dispatchCurrentUserPaymentEmailToBSC($request->user()->name, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)), $request->bsc_email, $official_count, $personal_count, $reviewed_amount, $official_percentage, $personal_percentage, $this->allowance_amount, $request->id);
        $this->dispatchCurrentUserPaymentEmail($request->user()->name, $request->user()->email, $reviewed_amount, date('d/M/Y', strtotime($from_date)), date('d/M/Y', strtotime($to_date)));
        return response()->json($user_phone_bill_updated, 200);
    }


    private function dispatchCurrentUserPaymentEmail($username, $email, $reviewedAmount, $fromDate, $toDate)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Payment Evidence Uploaded"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new UploadedPaymentEvidenceUserEmail($emailLog, $username, $email, $reviewedAmount, $fromDate, $toDate));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }



    private function dispatchCurrentUserPaymentEmailToBSC($fromName, $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount, $phoneBillId)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Payment Evidence Uploaded"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new UploadedPaymentEvidenceBSCEmail($emailLog, $fromName, $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount,$phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}
