<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    //
    public function index(Request $request)
    {
        // get title yang ingin dicari
        // cek apakah nilai search kosong
        // jika ada maka get article berdasarkan title yang disearch / cari dan buat paginationnya
        // jila tidak ada maka get article dan bbuat paginationnya
        // kembalikan nilai jsonnya

        $searchQuery = $request->query('search');

        if($searchQuery !== null)
        {
            $articles = Article::with(['category', 'user:id,name,email,picture'])
                ->select([
                    'id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'created_at', 'updated_at'
                ])
                ->where('titile', 'like', '%' . $searchQuery .'%')
                ->paginate()
                ;
        } else {
            $articles = Article::with(['category', 'user:id,name,email,picture'])
            ->select([
                'id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'created_at', 'updated_at'
            ])
            ->paginate()
            ;
        }
        return response()->json([
            'meta'=> [
                'code' => 200,
                'status'=> 'success',
                'messsage' => 'Article fetche succesfully',
            ],
            'data' => $articles,

        ]);
    }

    public function show($slug)
    {
        // get article berdasarkan slug
        //  cek apakah query get article berhasil
        // jika iya kembalikan response success
        // (dieksekusi jika get article gagal) kembalikan response 404 not found
        $article = Article::with(['category', 'user:id,name,email,picture'])
            ->where('slug', $slug)
            ->first()
            ;
        
        if($article)
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
                'code' => 404, 
                'status' => 'error',
                'message' => 'Article not found',
            ],
            'data' => [],
        ], 404);
    }
}
