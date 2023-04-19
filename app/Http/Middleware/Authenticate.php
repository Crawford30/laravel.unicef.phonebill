<?php

namespace App\Http\Middleware;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if ($request->has('untkn')) {
                $user = User::where('api_token', $request->untkn)->first();
                if ($user != NULL) {
                    Auth::login($user);
                    return url()->current();
                }
            }
            return env('PLATFORM_URL') . '?link=' . url()->current();
        }
    }
}


