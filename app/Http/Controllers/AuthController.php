<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginFromPlatform(Request $request)
    {

        try{
            \auth()->logout();
        }catch (\Exception $e){

        }
        if(decrypt($request->token) == $request->userId){
            Auth::loginUsingId($request->userId);
            $user = auth()->user();
            return redirect()->route('home');
        }
        return redirect('UnAuthorized!',403);
    }

    public function  logoutFromPlatform()
    {
        $user = auth()->user();
        $token = encrypt($user->id);
        auth()->logout();
        return redirect()->away(env("PLATFORM_URL")."/module-return?token=$token");
    }






}
