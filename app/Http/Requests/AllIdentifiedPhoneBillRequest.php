<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\CallLogPayment;

class AllIdentifiedPhoneBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return in_array('admin', auth()->user()->permissions);

        // if (isUserAuthorized("admin", "", "", false) == true) {
        //     //grant access to  admin
        //     return true;
        // }
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
            //
        ];
    }

    public function getAllUserIdentifiedCalls($request)
    {
        $allIdentifiedPhoneBills = CallLogPayment::all()->sortByDesc('updated_at');
        return response()->json($allIdentifiedPhoneBills, 200);
    }
}