<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller
{
   public function getData(){       
       $category = Category::query();
       
       $category->select('id','name','description')
               ->orderBy('id','desc');
       
       $result = $category
               ->get()
               ->toArray();

       return response()->json(['data'=>$result]);
               
   }
}
