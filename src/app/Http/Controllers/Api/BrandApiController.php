<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandApiController extends Controller {

    public function getData() {
        $brand = Brand::query();

        $brand->select('id', 'name', 'description')
                ->orderBy('id', 'asc');
        
        $result = $brand
                ->get()
                ->toArray();
        
        return response()->json(['data' => $result]);
    }

}
