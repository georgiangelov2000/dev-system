<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller {

    public function getData(Request $request) {
        $user = $request->user;

        $category = Category::with(['subCategories' => function ($query) {
            $query->select('category_sub_category.id', 'name');
        }]);

        if (isset($user) && $user) {
            $category->whereHas('suppliers', function ($query) {
                $query->where('supplier_id', $user);
            });
        }

        $category->select('id', 'name', 'description')
                ->orderBy('id', 'asc');

        $result = $category
                ->get()
                ->toArray();

        return response()->json(['data' => $result]);
    }

}
