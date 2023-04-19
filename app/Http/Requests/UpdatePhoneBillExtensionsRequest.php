<?php

namespace App\Http\Requests;

use App\PhoneBillExtensions;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhoneBillExtensionsRequest extends FormRequest
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
        return  [
            "id" => "required",
            "call_type" => "required",
            // "bill_owner_id" => "required",
        ];
    }




    public function updatePhoneBillExtension($request)
    {
        $data = [
            "id" =>  $request->id,
            "bill_owner_id" =>  $request->user()->id,
            "call_type" => $request->call_type,
            "name" => $request->name,
        ];

        $form = PhoneBillExtensions::updateOrCreate(["id" => $request->id], $data);

        return response()->json($form, 200);
    }
}