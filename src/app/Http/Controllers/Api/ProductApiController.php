<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller {

    public function getData() {
        
        $productQuery = $this->buildProductQuery();
        
        $result = $this->getProducts($productQuery);
                
        return response()->json(['data' => $result]);
    }

    private function buildProductQuery() {
        return Product::query()->with(['categories', 'subcategories', 'brands', 'images','suppliers:id,name'])
                ->select('id', 'name', 'supplier_id', 'quantity', 'notes', 'price','total_price','code', 'status', 'created_at')
                ->where('status', 'enabled')
                ->where('quantity', '>', 0)
                ->orderBy('id', 'asc');
    }

    private function getProducts($query) {
       return $query->get();
    }

}