<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use ImageKit\ImageKit;
use App\Http\Requests\Me\Profile\UpdateRequest;

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

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = User::find(auth()->id());

        //GET SEMUA REQUEST
        //cek apakah ada request picture
        //jika iya proses dengan cara buat unstance imagekit
        // rubah gambar ke base64
        //upload masukan file, filename, dan folder
        //dapatkan urlnya
        //masukan urlnya ke table database
        //jika tidak ada picture lanjut ke proses update

        
        if($request->hasFile('picture'))
        {
            $imageKit = new ImageKit(
                env('IMAGEKIT_PUBLIC_KEY'),
                env('IMAGEKIT_PRIVATE_KEY'),
                env('IMAGEKIT_URL_ENDPOINT'),
            );

            $image = base64_encode(file_get_contents($request->file('picture')));

            $uploadImage = $imageKit->uploadFile([
                'file' => $image,
                'fileName' => $user->email,
                'folder' => '/user/profile'
            ]);

            $validated['picture'] = $uploadImage->result->url;

        }

        //masukan semua request yang sudah divalidasi

        $update = $user->update($validated);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'succes',
                'message' => 'User data updated sucesfully.',
            ],
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->picture,
            ]
        ]);   
    }
}
