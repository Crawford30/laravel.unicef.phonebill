<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class FileDownloadController extends Controller
{
    public function getReceipt(Request $request)
    {
        $directory = 'storage/';
        $link = $request->input('link');

        $paths = explode('/', $link);
        $filename = $paths[count($paths) - 1];

        try {
            $request = new Client();
            if(!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            $response = $request->request('GET', $link, [
                'headers' => ['Content-Type' => 'application/pdf'],
                'sink' => $directory . $filename
            ]);
            return apiResponse('/' . $directory . $filename);
        } catch (\Exception $exception) { return apiResponse(""); }
    }
}
