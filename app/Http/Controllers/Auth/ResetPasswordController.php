<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and handles password reset functionality.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Reset password functionality placeholder
     */
    public function showResetForm()
    {
        return view('auth.passwords.reset');
    }
}