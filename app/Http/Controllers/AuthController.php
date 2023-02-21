<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
      /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Auth Register",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={
     *                      "name": "Utku Yılgın",
     *                      "email": "utku.387@hotmail.com",
     *                      "password": "123456",
     *                  },
     *             )
     *         ),
     *
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="token",
     *                     property="token",
     *                     type="string"
     *                 ),
     *                 example={
     *                      "token": "17|srqlOVFa17YdGCpgUYiOIPp7Yw2GcAmr1xbJbr8C",
     *                  },
     *                 @OA\Property(
     *                     description="信息",
     *                     property="msg",
     *                     type="object",
     *                 )
     *             )
     *         )
     *     ),
     *     )
     * )
     */
    public function register(Request $request) {

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);
        $token = $user->createToken('userToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Auth Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={
     *                      "email": "utku.387@hotmail.com",
     *                      "password": "123456",
     *                  },
     *             )
     *         ),
     *
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="token",
     *                     property="token",
     *                     type="string"
     *                 ),
     *                 example={
     *                      "token": "17|srqlOVFa17YdGCpgUYiOIPp7Yw2GcAmr1xbJbr8C",
     *                  },
     *                 @OA\Property(
     *                     description="信息",
     *                     property="msg",
     *                     type="object",
     *                 )
     *             )
     *         )
     *     ),
     *     )
     * )
     */
    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !\Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Kullanıcı Adı Veya Şifre Yanlış!'
            ], 401);
        }

        $token = $user->createToken('userToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

}
