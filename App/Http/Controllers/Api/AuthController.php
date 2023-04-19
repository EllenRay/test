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

    /**
     * Create a new User
     * @OA\Post(
     *  path="/api/auth/register",
     *  tags={"Users"},
     *     @OA\Parameter(in="path",name="name",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(in="path",name="email",required=true,@OA\Schema(type="email")),
     *     @OA\Parameter(in="path",name="password",required=true,@OA\Schema(type="password")),
     *     @OA\Parameter(in="path",name="password_confirmation",required=true,@OA\Schema(type="password")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="email"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="password"
     *                      ),
     *                      @OA\Property(
     *                          property="password_confirmation",
     *                          type="password"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"José da Silva",
     *                     "email":"email@company.com",
     *                     "password":"1asda$ghf%$",
     *                     "password_confirmation":"1asda$ghf%$",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="José da Silva"),
     *              @OA\Property(property="email", type="email", example="mail@company.com"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="id", type="number", example="1"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The email has already been taken."),
     *          )
     *      ),
     * )
     */

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


          /**
        * @OA\Post(
        * path="/api/auth/login",
        * operationId="authLogin",
        * tags={"Users"},
        * summary="User Login",
        * description="Login User Here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email", "password"},
        *               @OA\Property(property="email", type="email"),
        *               @OA\Property(property="password", type="password")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="6|bDbbUML7N6ZAf1WgNgmAgAVGjbicIkfoPVa9H52G"),
     *          )
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function login(UserLoginRequest $request)
    {
        $payload = $request->all();

        if (!Auth::attempt($payload)) {
            return $this->error('Credentials not match', 401);
        }

        $user = auth()->user();


        return $this->success([
            'token' => $user->createToken('API Token')->plainTextToken,
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
