<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryApiController extends Controller
{
    public function getData(Request $request)
    {
        $category = $request->category;
        $subCategoryQ = SubCategory::query();
    
        if ($category) {
            $subCategoryQ->where('category_id', $category);
        }
    
        $result = $subCategoryQ->with('category')->get();
        
        return response()->json(['data' => $result]);
    }
    
}