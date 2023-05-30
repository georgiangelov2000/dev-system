<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller {

    public function getData(Request $request) {
        $supplier = $request->supplier;
        $category = $request->category;
        $search = $request->search;
        
        $categoryQuery = $this->buildCategoriesQuery();

        if ($supplier || $supplier == '0') {
            $this->filterCategoriesBySupplier($categoryQuery, $supplier);
        }
        if($search) {
            $categoryQuery->where('name', 'LIKE', '%'.$search.'%');
        }
        if ($category) {
            $this->findCategory($categoryQuery,$category);
        }

        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);
        $totalRecords = Category::count(); 
        $filteredRecords = $categoryQuery->count();
        $result = $categoryQuery->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
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
}
