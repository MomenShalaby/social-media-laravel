<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ForgotPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use HttpResponses;

    /**
     * Send a password reset link to the user's email.
     *
     * @param  \App\Http\Requests\auth\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $input = $request->only('email');
        $user = User::where('email', $input)->first();
        $user->notify(new ResetPasswordNotification());
        return $this->success([], 'an otp is sent to your email address');
    }
}
