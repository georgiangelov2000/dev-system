<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandApiController extends Controller
{

    public function getData()
    {
        $brands = Brand::query()
            ->select('id', 'name', 'description')
            ->orderBy('id', 'asc');

        $result = $brands->get()->map(function ($brand) {
            $brand->purchases_count = $brand->purchases()->count();
            return $brand;
        });

        return response()->json(['data' => $result]);
    }
}
