<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GoogleAuthController extends Controller
{
    //

    public function signIn(Request $request)
    {   
        // get json request, token jwt
        // pecah token dengan separator
        // decode base64 ke string menggunakan base64_decoder, yang didecode adalah pecahan dengan index 1 
        
        $request = $request->json()->all();
        $tokenParts = explode('.', $request['token']);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload, true);

        if($jwtPayload === null)
        {
            return response()->json([
                'meta' => [
                    'code' => 422, //unprocesseable entity
                    'status' => 'error',
                    'message' => 'Token invalid. ',
                ],
                'data' =>[],
            ],422);
        }

        $findUser = User::where('social_id', $jwtPayload['sub'])->first();

        if($findUser)
        {
            $token = auth()->login($findUser);

            return response()->json([
                    'meta' => [
                        'code' => 200,
                        'status' => 'succes',
                        'message' => 'Signed in succesfully',
                    ],
                    'data' => [
                        'user' => [
                            'name' => $findUser->name,
                            'email' => $findUser->email,
                            'picture' => $findUser->picture,  
                        ],
                        'access_token' => [
                            'token' => $token,
                            'type' => 'Bearer',
                            'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                        ],
                    ],
                ]); 
        }

        $newUser = User::create([
            'name' => $jwtPayload['name'],
            'email' => $jwtPayload['email'],
            'password' => bcrypt('my-google'),
            'picture' => $jwtPayload['picture'],
            'social_id' => $jwtPayload['sub'],
            'social_type' => 'google',
        ]);

        $token = auth()->login($newUser);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'succes',
                'message' => 'Signed in succesfully',
            ],
            'data' => [
                'user' => [
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'picture' => $newUser->picture,  
                ],
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ],
            ],
        ]); 
    
    }
}
