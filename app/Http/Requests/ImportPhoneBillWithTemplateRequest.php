<?php

namespace App\Http\Requests;

use App\EmailLog;
use App\StaffProfile;
use App\CallLogTimeline;
use App\Man3000AreaCode;
use App\Man3000Extension;
use App\PhoneBillUserData;
use App\PhoneBillExtensions;
use Illuminate\Support\Facades\DB;
use App\Imports\PhoneBillDataImport;
use App\Mail\MonthlyCallLogICTEmail;
use App\PhoneBillImportFileDataTemp;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use App\Mail\MonthlyCallLogStaffEmail;
use Illuminate\Foundation\Http\FormRequest;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class ImportPhoneBillWithTemplateRequest extends FormRequest
{
    private $counter;

    protected $expectedHeaders = [
        "Ext", "Name", "T", "Number", "Area/Acc", "Date Time", "MM:SS", "Ring", "Cost"
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return in_array('phone_billing', auth()->user()->permissions);

        //return in_array('s_admin', auth()->user()->permissions); //super admin


        if (isUserAuthorized("s_admin", "", false) == true) {
            //grant access to supper admin
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
            "file" => "required",
            "identification_deadline" => "required",
        ];
    }



    public function import($request)
    {


        $uniqueExtensionCount = 0;
        $uniquePhoneCount = 0;
        // $totalCost = 0;



        $phoneBillDataImport = new PhoneBillDataImport();
        $collectionData = Excel::toCollection($phoneBillDataImport, $request->file);
        $data = Excel::toArray($phoneBillDataImport, $request->file);


        $extArray = [];
        $userNamesArray = [];
        $areaACCArray = [];
        $namesAndCode = [];
        $nameArrary = [];
        $type = [];
        $telePhone = [];
        $dateTime = [];
        $duration = [];
        $cost = [];
        $totalCostForUser = [];
        $uniqueExtensions = [];

        foreach ($data[0] as $key => $value) {
            $extArray[] = str_replace([' '], '', $value[0]);
            $nameArrary[] = str_replace([' '], '', $value[1]);
            $namesAndCode[] = trim($value[2]);
            $userNamesArray[] = trim($value[4]);
            $type[] = str_replace([' '], '', $value[5]);
            $telePhone[] = str_replace([' '], '', $value[6]);
            $areaACCArray[] = str_replace([' '], '', $value[9]);
            $dateTime[] =  str_replace([' '], '', $value[11]);
            $totalCostForUser[] = str_replace([' '], '', $value[15]);
            $duration[] = str_replace([' '], '', $value[14]);
            $cost[] = str_replace([' '], '', $value[18]);
        }





        //===========Remove empty string from the $username array and keep the space between the text eg " JOHN KAMA " will be "JOHN KAMA"
        $filteredNames = array_filter($userNamesArray, function ($element) {
            return is_string($element) && '' !== trim($element);
        });

        $filteredNamesAndCodes = array_filter($namesAndCode, function ($element) {
            return is_string($element) && '' !== trim($element) && trim($element) !== "Total";
        });

        $filteredTotalCostForUser = array_filter($totalCostForUser, function ($element) {
            return is_string($element) && '' !== trim($element) && trim($element) !== "Cost";
        });





        $filteredAreaAcc = array_filter($areaACCArray, function ($element) {
            return is_string($element) && '' !== trim($element) && trim($element) !== "Area/Acc" && strlen(trim($element)) >= 4 && trim($element) !== "Unans";
        });



        //===================Get the "FromDate" to "ToDate" from the Excel file=================
        $fromToDateFiltered = $extArray[4];

        $var2 =   str_replace(['From:', 'To:', ' '], '', $fromToDateFiltered);

        $middle = strlen($var2) / 2;
        $first = substr($var2, 0, $middle);
        $last = substr($var2, $middle);

        $fromDate =   trim(chunk_split($first, ((strlen($first) / 2) + 1), ' '));
        $toDate = trim(chunk_split($last, ((strlen($last) / 2) + 1), ' '));





        $filteredExtension =  (array_filter($extArray, function ($value) {
            //======Returns only word less than length of 10, to ignore some data in excel file
            // return !is_null($value) && $value !== '' && $value !== 'Ext' && strlen($value) <= 10;

            //======Returns only word less than length of 4, to ignore some data in excel file
            return !is_null($value) && $value !== '' && $value !== 'Ext' && strlen($value) <= 4;
        }));


        $filteredNumber = (array_filter($telePhone, function ($value) {
            return !is_null($value) && $value !== '' && $value !== 'Number';
        }));
        $filteredName = (array_filter($nameArrary, function ($value) {
            return !is_null($value) && $value !== '' && $value !== 'Name';
        }));
        $filteredType = (array_filter($type, function ($value) {
            return !is_null($value) && $value !== '' && $value !== 'T';
        }));
        $filteredDateTime = (array_filter($dateTime, function ($value) {
            return !is_null($value) && $value !== '' && $value !== 'DateTime';
        }));
        $filteredDuration = (array_filter($duration, function ($value) {
            return !is_null($value) && $value !== '' && $value !== 'MM:SS';
        }));
        $filteredCost =  (array_filter($cost, function ($value) {

            return !is_null($value) && $value !== '' && $value !== 'Cost';
        }));


       

        $merged = array();

        // foreach ($filteredExtension as $key => $el) {

        //     $data =   (object) [
        //         "id" => $id++,
        //         "ext" => $filteredExtension[$key],
        //         "phone_number" => $filteredNumber[$key],
        //         "name" => "",
        //         "type" => $filteredType[$key],
        //         "date_time" => trim((chunk_split($filteredDateTime[$key], 10, ' '))),
        //         "duration" => $filteredDuration[$key],
        //         "cost" => $filteredCost[$key],
        //         "call_type" => "",
        //         "is_call_type_accepted" => "",
        //     ];

        //     //
        //     array_push($merged, $data);
        // }

        $collectionData = Excel::toCollection($phoneBillDataImport, $request->file);
        $sheetOne = $collectionData->first();


        $headers = $sheetOne->first(function ($value, $key) {
            return $key  == 9;
        });

        $content = $collectionData->first()->slice(5);

        $userId = auth()->user()->id;

        $fileHeaders = $headers->filter(function ($value, $key) {
            return $value != null;
        });


        $fileHeaders = $fileHeaders->toArray();

        if (!$this->isTemplateValid($fileHeaders)) {
            return response()->json([
                'message' => 'Invalid Template Used'
            ], 404);
        };


        $totalCost = array_sum($filteredCost);
        $this->$uniqueExtensionCount = count(array_unique($filteredExtension));
        $this->$uniquePhoneCount = count(array_unique($filteredNumber));
        $totalRecords = count($merged);
        $uniqueExtensions = array_unique($filteredExtension);



        $data = [
            "user_id" =>  $userId,
            "identification_deadline_date" => $request->identification_deadline,
            "from_date" => $fromDate,
            "to_date" => $toDate,
            "extensions" => array_values($uniqueExtensions),
            "total_monthly_cost" => round($totalCost, 2),
            "unique_mobile_number_count" =>  $this->$uniquePhoneCount,
            "unique_extensions_count" =>  $this->$uniqueExtensionCount,
            "total_records" =>  $totalRecords,

        ];

        $phone_bill_user_data = PhoneBillUserData::updateOrCreate(["id" => $this->id], $data);


        




        foreach ($filteredNamesAndCodes as $key => $el) {
            $full1 = explode(' ', $el, 2);
            $code = $full1[0];
            $name = ltrim($full1[1]);
         


          

            $man3000Extensions =    [
                "name" => $name,
                "area_code" => $code,
                "user_data_id" => $phone_bill_user_data->id,

            ];


           // print_r( $man3000Extensions );


            //return $man3000Extensions;




            $man3000ExtensionsData = Man3000Extension::updateOrCreate(["id" => $this->id], $man3000Extensions);
        }


   





        // foreach (array_unique($filteredAreaAcc) as $key => $el) {

        //     $areaData =    [
        //         "area_code" => $el,
        //         "user_data_id" => $phone_bill_user_data->id,
        //     ];
        //     $man3000AreaCodeData = Man3000AreaCode::updateOrCreate(["id" => $this->id], $areaData);
        // }



       

        //$phone_bill = PhoneBillImportFileDataTemp::updateOrCreate(["id" => $this->id], $data);


        // return $filteredExtension ;

        $totalExtensions = 0;

        foreach ($filteredExtension as $key => $el) {

            $phone_bills_data =    [
                "ext" => $filteredExtension[$key],
                "user_id" => $userId,
                "user_data_id" => $phone_bill_user_data->id,
                "phone_number" => $filteredNumber[$key],
                "area_code" => $filteredAreaAcc[$key],
                "type" => empty($filteredType[$key]) ? 0 : $filteredType[$key],
                "date_time" => trim((chunk_split($filteredDateTime[$key], 10, ' '))),
                "duration" => $filteredDuration[$key],
                "cost" => $filteredCost[$key],
            ];


            $totalExtensions++;
            $phone_bill_extensions_data = PhoneBillExtensions::updateOrCreate(["id" => $this->id], $phone_bills_data);
        }

       



        //=================Update Total Cost for Each call log========
        $allStaffExtenstions = Man3000Extension::all();
        foreach ($allStaffExtenstions as $key => $singleStaffExten) {
            $sum = DB::table('phone_bill_extensions')->where('user_data_id', '=', $singleStaffExten['user_data_id'])->where('area_code', '=', $singleStaffExten['area_code'])->sum('cost');

            $man3000UpdatedCost =   Man3000Extension::updateOrCreate(['area_code' => $singleStaffExten['area_code'], 'user_data_id' => $phone_bill_user_data->id],  [
                "total_monthly_cost" => round($sum, 2),

            ]);

            $man3000AreaCodeUpdatedCost = Man3000AreaCode::updateOrCreate(['area_code' => $singleStaffExten['area_code'], 'user_data_id' => $phone_bill_user_data->id],  [
                "total_monthly_cost" => round($sum, 2),

            ]);

        }

        // return;



    //=================Update Unique Mobile Count for Each call log========
        $staffProfiles = StaffProfile::all();
        foreach ($staffProfiles  as $key => $staffprofile) {
            $allStaffExtenstions = Man3000Extension::whereNotNull("name")->where('name', $staffprofile->name)->get()->flatten();
            foreach ($allStaffExtenstions as $key => $singleStaffExten) {
                $count = DB::table('phone_bill_extensions')->where("area_code",   $singleStaffExten['area_code'])->where("user_data_id", $phone_bill_user_data->id)->count(DB::raw('DISTINCT phone_number'));
                $man3000UpdatedCallCount =   Man3000Extension::updateOrCreate(['area_code' => $singleStaffExten['area_code'], 'user_data_id' => $phone_bill_user_data->id],  [
                    "mobile_number_unique_count" => $count,
                ]);
            }
        }







        $updated_phone_bill_user_data = PhoneBillUserData::updateOrCreate(["id" => $phone_bill_user_data->id], ["total_records" =>  $totalExtensions]);

        //date('Y/m/d', strtotime($request->from_date)), date('Y/m/d', strtotime($request->to_date))
        return response()->json([
            "from_date" => date('Y/m/d', strtotime($fromDate)),
            "to_date" => date('Y/m/d', strtotime($toDate)),
            "unique_mobile_number_count" =>  $this->$uniquePhoneCount,
            "unique_extensions_count" => $this->$uniqueExtensionCount,
            "total_records" =>  $totalExtensions,
            "total_monthly_cost" => $totalCost,
            'phone_bill' => $phone_bill_user_data,
            'phone_bill_id' => $phone_bill_user_data->id,
            'phone_bill_data' => $data

        ], 200);
    }






    private function isTemplateValid(array $header)
    {
        return count($this->expectedHeaders) == count($header);

        // return count($this->expectedHeaders) == count(array_intersect_assoc($this->expectedHeaders,$header));
    }






    private function mappedHeaders()
    {

        return [
            "ext" => "Ext",
            "name" => "Name",
            "total" => "T",
            "phone_number" => "Number",
            "area_or_acc" => "Area/Acc",
            "date" =>  "Date",
            "minute_second" => "MM:SS",
            "ring" => "Ring",
            "cost" => "Cost"
        ];
    }







    private function formatDateValues($value)
    {
        return is_numeric($value) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format("Y-m-d") :  $value;
    }
}