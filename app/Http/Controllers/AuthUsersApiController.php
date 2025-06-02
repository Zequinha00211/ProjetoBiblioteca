<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;


class AuthUsersApiController extends Controller
{
    function __construct()
    {
        \Config::set('jwt.user', \App\Models\UsersApi::class);
        \Config::set('auth.providers', [
            'users' => [
                'driver' => 'eloquent',
                'model' => \App\Models\UsersApi::class,
            ]
        ]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

   public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where('id', $request->id)->update(
            [
                'password' => bcrypt($request->password),
                'name' => $request->name
            ]
        );

        return response()->json([
            'success' => true,
            'data' =>   $user
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'success' => true,
            'data' =>   $user
        ], 201);
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

   
    public function userProfile()
    {
        return response()->json(['success' => true, 'data' => auth()->user()]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            "success" => true,
            'data' => [
                'access_token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                /*  'token_type' => 'bearer', */
                /* 'user' => auth()->user() */
            ]
        ]);
    }
}
