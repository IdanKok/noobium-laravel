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
}
