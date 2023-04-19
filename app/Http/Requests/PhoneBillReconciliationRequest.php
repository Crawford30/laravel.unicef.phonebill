<?php

namespace App\Http\Requests;

use App\CallLogPayment;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;

class PhoneBillReconciliationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return true;
        
        //in_array('admin', auth()->user()->permissions); 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }



    public function getPhoneBillBSC($request)
    {
        //====Show Only Call that has been reviewed ie where reviewed amount is not null
        $phoneBillReconcilled = CallLogPayment::select('id', 'bill_owner_id', 'user_data_id', 'personal_calls_count', 'official_calls_count', 'bill_type', 'identified_amount', 'reviewed_amount', 'status', 'call_log_for', 'section', 'from_date', 'to_date', 'document_one', 'document_two', 'invoice_status', 'reviewed_by', 'created_at', 'updated_at')->orderBy('updated_at', 'desc')->whereNotNull('reviewed_amount')->get();
        return response()->json($phoneBillReconcilled, 200);
    }
}
