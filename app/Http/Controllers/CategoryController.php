<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // get semua category dari semua user
        // return response json semua category
        $categories = Category::all();
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Category fetched succesfully',
            ],
            'data' => $categories
        ]);
    }

    public function show($categorySlug)
    {
        // get category slug dimana slugnya sama dengan category slug yang didapat dari route
        // cek apakaha category tersebut ada
        // jika adaFullscreeurn article tersebut
        // (jika kode ini dieksekusi maka category yang dicari tidak ada) maka kembalikan response error 404 category not found
        $category = Category::where('slug', $categorySlug)->first();

        if($category)
        {
            $articles = Category::find($category->id)
                ->articles()
                ->with(['category','user:id,name,picture'])
                ->select([
                    'id', 'user', 'category_id', 'title', 'content_preview', 'slug', 'featured_image', 'created_at', 'updated_at'
                ])
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
        return response()->json([
            'meta' => [
                'code' => 401,
                'status' => 'error',
                'message' => 'Category not found',
            ],
            'data' => [], 
        ]);
            
    }
}
