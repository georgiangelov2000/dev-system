<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller {

    public function getData(Request $request) {
        $supplier = $request->supplier;
        $category = $request->category;

        $categoriesQuery = $this->buildCategoriesQuery();

        if ($supplier || $supplier == '0') {
            $this->filterCategoriesBySupplier($categoriesQuery, $supplier);
        }

        if ($category) {
            $this->findCategory($categoriesQuery,$category);
        }

        $categories = $this->getCategories($categoriesQuery);
        return response()->json(['data' => $categories]);
    }

    private function buildCategoriesQuery() {
        return Category::query()
        ->select('id','name','description')
        ->with('subCategories');
    }

    private function filterCategoriesBySupplier($query, $supplier) {        
        $query->whereHas('suppliers', function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier);
        });
    }

    private function findCategory($query,$category){
        return $query->where('id',$category);
    }

    private function getCategories($query) {
        return $query->get()->toArray();
    }

}
