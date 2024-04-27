<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Models\Profile;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Handle user login.
     *
     * @param  \App\Http\Requests\Auth\LoginUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all);
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error("unuthorized", 403);
        }
        $user = User::where("email", $request->email)->first();
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
        ]);

    }

    /**
     * Handle user registration.
     *
     * @param  \App\Http\Requests\Auth\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        $profile = new Profile([
            'user_id' => $user->id,
        ]);
        $user->profile()->save($profile);
        return $this->success(['user' => $user, 'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken], 'login successful');
    }


    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([], 'Logout successful');
    }

}
