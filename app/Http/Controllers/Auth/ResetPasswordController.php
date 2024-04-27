<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ResetPasswordRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use HttpResponses;
    private $otp;
    public function __construct()
    {
        $this->otp = new Otp();
    }


    /**
     * Reset the user's password.
     *
     * @param  \App\Http\Requests\auth\ResetPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $otp = $this->otp->validate($request->email, $request->otp);
        if (!$otp->status) {
            return response()->json(['error' => $otp], 401);

        }
        $user = User::where('email', $request->email)->first();
        $user->update(
            [
                'password' => Hash::make($request->password)
            ]
        );
        $user->tokens()->delete();
        return $this->success([], 'Passwords updated successfully');

    }
}
