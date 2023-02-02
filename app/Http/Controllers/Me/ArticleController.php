<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\Article\StoreRequest;
use Str;
use App\Models\User;
use ImageKit\ImageKit;
use App\Models\Article;

class ArticleController extends Controller
{

    public function index()
    {
        //get user id yg saat ini login
        // get article dimana user id nya yg saat ini sedang login 
        // get category dan usernya siapa
        //buat paginationny

        $userId = auth()->id();

        $articles = Article::with(['category', 'user:id,name,email,picture'])->select([
            'id', 
            'user_id', 
            'category_id', 
            'title', 
            'slug',  
            'content_preview', 
            'featured_image', 
            'created_at', 
            'updated_at', 
        ])
            ->where('user_id', $userId)
            ->paginate();

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'succes',
                    'message' => 'Article fetched succesfully',
                ],
                'data' => $articles,
            ]);
    }
    //
    public function store(StoreRequest $request)
    {
        //category_id
        //title
        //content
        //featured_image
        //validasi semua requesat yg masuk
        //terima dicontroller 
        //generate slug dari request title
        //generate content preview dari request content
        //get file image lalu ubah ke base64 untuk dikirim ke imagekit
        //get url image kit dari image yang kita kirim ke imagekit
        //get user id yng login
        // lalu create articke berdasarkan data yang kita proses dimana userny adalah user yang login
        
        $validated = $request->validated();
        $validated['slug'] = Str::of($validated['title'])->slug('-') . '-' . time();
        $validated['content_preview'] = substr($validated['content'], 0 , 218) . '...';
        $imageKit = new ImageKit(
            env('IMAGEKIT_PUBLIC_KEY'), 
            env('IMAGEKIT_PRIVATE_KEY'),
            env('IMAGEKIT_URL_ENDPOINT'),
        );
        $image = base64_encode(file_get_contents($request->file('featured_image')));

        $uploadImage = $imageKit->uploadFile([
            'file' => $image,
            'fileName' => $validated['slug'],
            'folder' => '/article', 
        ]);
        $validated['featured_image'] = $uploadImage->result->url;
        $userId = auth()->id();
        $createArticle = User::find($userId)->articles()->create($validated);

        if ($createArticle)
        {
            return response()->json([
                'meta' => [
                    'code' =>200,
                    'status' => 'succes',
                    'message' => 'Article created succesfully.',
                ],
                'data' => [],
            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'error',
                'message' => 'Error! Article failed to created.',
            ],
            'data' => [],
        ], 500);

    }

    public function show($id)
    {
        //get article berdasarkan id yang diberikan
        //cek apakah article berhasil diget 
        //jika article idak bisa diget maka kembalikan response nkot found
        //jika article berhasil di get maka dapatkan id user saat ini login
        //cek apakah id user yang saat ini login sama dengan id user yang ada di data article yang kita get
        //jika tidak sama maka kembalikan response unauthorized 
        //jika sama maka kembalikan article denga success

        $article = Article::with(['category', 'user:id,name,picture'])->find($id);
        
        if($article)
        {
            $userId = Auth()->id();

            if($article->user_id === $userId)
            {
                return response()->json([
                    'meta' => [
                        'code' => 200,
                        'status' => 'succes',
                        'message' => 'Article fetched succesfully',
                    ],
                    'data' => $article,
                ]);
                
            }

            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ],
                'data' => [],
            ], 404);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article not found'
            ],
            'data' => [],
        ], 404);

    }
}
