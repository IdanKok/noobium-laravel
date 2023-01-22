<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function show()
    {
        $user = auth()->user();
        
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'succes',
                'messsage' => 'User data fetched succesfully.',
            ],
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->picture,

            ]
        ]); 
    }
}
