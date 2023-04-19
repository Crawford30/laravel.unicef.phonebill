<?php

namespace App\Http\Requests;

use App\EmailLog;
use App\CallLogTimeline;
use App\CallLogPayment;
use App\CallLogStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use App\Mail\UploadedPaymentEvidenceBSCEmail;
use App\Mail\UploadedPaymentEvidenceUserEmail;

class UploadPaymentReceiptRequest extends FormRequest
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
            "document_one" => "required",
            "allowance_amount" => "required",
            "id" => "required",

        ];
    }


    public function uploadReciept($request)
    {


        //return  $request->all();

        if ($request->hasFile('document_one')) {
            $file = storeFile('receipt_one', $request->file('document_one'));
            $imagePath = $file->file_path;
        } else {
            $imagePath = null;
        }


        $data = [
            'id' => $request->id,
            'document_one' => $imagePath,
            'invoice_status' => "Payment Receipt Uploaded",
            'user_data_id'  => $request->user_data_id,
        ];

        $user_phone_bill_updated =  CallLogPayment::updateOrCreate(["user_data_id" => $request->user_data_id, 'bill_owner_id' => $request->user()->id], $data);

        $getUserData = CallLogPayment::where('id', $request->id)->select('to_date', 'from_date', 'reviewed_amount', 'official_calls_count', 'personal_calls_count')->get();

        // return $getUserData;


        $to_date =  $request->to_date;
        
        //$getUserData->first()->to_date;
        $from_date = $request->from_date;
        
        
        // $getUserData->first()->from_date;
        $reviewed_amount = $request->reviewed_amount;
        
        //$getUserData->first()->reviewed_amount;
        $personal_count = intval($request->personal_count);
        
        //$getUserData->first()->personal_calls_count;
        $official_count = intval($request->official_count);
        
        //$getUserData->first()->official_calls_count;
        $total_count = ( intval($personal_count) + intval($official_count));
        
       


        if($total_count === 0 ){
            $official_percentage = 0;
            $personal_percentage = 0;
        }else{
            $official_percentage = round((($official_count) / ($total_count)) * 100, 2);
            $personal_percentage = round((($personal_count) / ($total_count)) * 100, 2);

        }

       

       
       

        //$call_log_time_line_updated = CallLogTimeline::findOrFail($request->id);

        //$call_log_time_line_updated->update($call_timeline_data);

        $call_timeline_data = [
            "payment_amount" => $reviewed_amount,
            "payment_by" => $request->user()->name,
            "payment_notification" => "Payment Receipt Uploaded",

        ];


        CallLogTimeline::updateOrCreate(["user_data_id" => $request->user_data_id, "identified_by_id" => $request->user()->id], $call_timeline_data);

        CallLogStatus::updateOrCreate(["user_data_id"  => $request->user_data_id, "bill_owner_id" => $request->user()->id], ["call_log_with" => "Payment Evidence Sent"]);


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



    private function dispatchCurrentUserPaymentEmailToBSC($fromName, $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $officialCallsPercentage, $personalCallsPercentage,  $allowanceAmount,$phoneBillId)
    {
        $emailLog = EmailLog::create([
            "to" => $email,
            "description" => "Phone Bill Payment Evidence Uploaded"
        ]);

        $emailLog->updateCode();

        try {
            $mail = (new UploadedPaymentEvidenceBSCEmail($emailLog, $fromName, $fromDate, $toDate, $email, $officialCalls, $personalCalls, $amountOwed, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount, $phoneBillId));
            $emailLog->update([
                "body" => $mail->render()
            ]);
            Mail::to($email)->send($mail);
        } catch (\Exception $exception) {
        }
    }
}