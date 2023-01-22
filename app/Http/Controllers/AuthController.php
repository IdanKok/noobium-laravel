<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Requests\Auth\SignInRequest;

class AuthController extends Controller
{

    public function __construct(User $user)
    {
        // model as dependency injection
        $this->user = $user;
    }
    //
    public function signUp(SignUpRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'picture' => env('AVATAR_GENERATOR_URL') . $validated['name'],
        ]);

        $token = auth()->login($user);

        if(!$token)
        {
            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Cannot add user.',
                ],
                'data' => [],
            ], 401);
        }

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'succes',
                'message' => 'User created succesfully',
            ],
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture,
                ],
                'acces_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),

                ]
            ],
        ]);
    }

    public function signIn(SignInRequest $request)
    {
        //request body
        //email
        //password
        //hit api
        //cocokan credential
        //kalau ngga cocok return 401 error
        //kalau cocok generate token dan kembalikan data user untuk disimpen di front end

        $token = auth()->attempt($request->validated());
        if(!$token)
        {
            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Incorrect email or password',
                ],
                'data' => [],
            ]);
        }

        $user = auth()->user();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'succes',
                'message' => 'Signed in succesfully',
            ],
            'data' => [
                'user' => [
                    'name'=> $user->name,
                    'email'=> $user->email,
                    'picture' => $user->picture,
                ],
                'acces_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ]    

            ],
        ]);
    }

    public function signOut()
    {
        auth()->logout();
        return response()->json([
            'meta'=> [
                'code' => 200,
                'status' => 'succes',
                'message' =>'Signed Out Succesfully'
            ],
            'data'=> [],
        ]);
    }

    public function refresh()
    {
        $user = auth()->user();
        $token = auth()->fromUser(auth()->user());

        return response()->json([
            'meta'=> [
                'code' => 200,
                'status' => 'succes',
                'message' =>'Token refresh succesfully.',
            ],
            'data'=> [
                'user' => [
                    'name' => $user->name,
                    'email'=> $user->email,
                    'picture' => $user->picture,
                ],
                'acces_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ]    
            ],
        ]);
    }
}
