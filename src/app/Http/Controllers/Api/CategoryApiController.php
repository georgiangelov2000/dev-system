<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryApiController extends Controller {

    public function getData(Request $request) {
        $supplierId = $request->supplier;
        $categoryId = $request->category;

        $categoriesQuery = $this->buildCategoriesQuery();

        if ($supplierId || $supplierId == '0' ) {
            $this->filterCategoriesBySupplier($categoriesQuery, $supplierId);
        }

        if ($categoryId) {
            $subcategories = $this->getSubcategories($categoryId);
            return response()->json(['data' => $subcategories]);
        }

        $categories = $this->getCategories($categoriesQuery);
        return response()->json(['data' => $categories]);
    }

    private function buildCategoriesQuery() {
        return Category::with(['subCategories' => function ($query) {
                $query->select('category_sub_category.id', 'name');
                }])
                ->select('id', 'name', 'description')
                ->orderBy('id', 'asc');
    }

    private function filterCategoriesBySupplier($query, $supplierId) {        
        $query->whereHas('suppliers', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        });
    }

    private function getSubcategories($categoryId) {
        return DB::table('category_sub_category')
                        ->join('subcategories', 'category_sub_category.sub_category_id', '=', 'subcategories.id')
                        ->select('subcategories.name', 'subcategories.id')
                        ->where('category_sub_category.category_id', '=', $categoryId)
                        ->get()
                        ->toArray();
    }

    private function getCategories($query) {
        return $query->get()->toArray();
    }

}
