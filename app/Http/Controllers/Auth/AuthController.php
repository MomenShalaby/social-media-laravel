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

    /**
     * Log in a user.
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Log in a user",
     *     description="Login by email, password",
     *     operationId="authLogin",
     *    @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       
     *    ),
     * ),
     *  @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *        @OA\Property(property="token", type="access_token", example="eyJ0eXAiOiJKV1QiLCJhbGc"),
     *        
     *     )
     *  ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="unuthorized"),
     *            @OA\Property(property="status", type="string", example="Error")
     *            
     *         )
     *     )
     * )
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
     * @OA\Post(
     * path="/api/logout",
     * summary="Logout",
     * description="Logout user and invalidate token",
     * operationId="authLogout",
     * tags={"Authentication"},
     * security={ {"bearer": {} }},
     * @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         description="Content type that the client expects in response",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"application/json"}
     *         )
     *     ),    
     *      * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *    )
     * )
     * )
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([], 'Logout successful');
    }

}
