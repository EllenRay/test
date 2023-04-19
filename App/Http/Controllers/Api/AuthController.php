<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    use ApiResponser;


    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'name'      => $request->name,
            'password'  => bcrypt($request->password),
            'email'     => $request->email
        ]);

        return $this->success([
            'user'  => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ],'User was created.');
    }

    public function login(UserLoginRequest $request)
    {
        $payload = $request->all();

        if (!Auth::attempt($payload)) {
            return $this->error('Credentials not match', 401);
        }

        $user = auth()->user();


        return $this->success([
            'token' => $user->createToken('API Token')->plainTextToken,
            'user'  => $user
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

}
