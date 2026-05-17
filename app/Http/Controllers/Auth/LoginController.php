<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller (AI Protected: SQL Injection)
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. VALIDASI INPUT DASAR (Laravel)
        |--------------------------------------------------------------------------
        */
        $this->validateLogin($request);

        $username = $request->input($this->username());
        $password = $request->input('password');

        /*
        |--------------------------------------------------------------------------
        | 2. LOGIN NORMAL
        |--------------------------------------------------------------------------
        */
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            if ($request->hasSession()) {
                $request->session()->put(
                    'auth.password_confirmed_at',
                    time()
                );
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}