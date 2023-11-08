<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller {

    public function getData(Request $request) {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $select_json = $request->input('select_json');
        $categoryQ = Category::query()->select('id','name','description');
        
        $this->applyFilters($request,$categoryQ);

        if(boolval($select_json)) {
            return $this->applySelectFieldJSON($categoryQ);
        }

        $categoryQ->with('subCategories')->withCount('products');

        $totalRecords = Category::count(); 
        $filteredRecords = $categoryQ->count();
        $result = $categoryQ->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function applyFilters($request, $query){
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });
        $query->when($request->input('id'), function ($query) use ($request) {
            return $query->where('id', $request->id);
        });
        $query->when($request->input('supplier'), function ($query) use ($request) {
            return $query->whereHas('suppliers', fn ($query) => $query->where('supplier_id', $request->input('supplier_id')));
        });
    }

    private function applySelectFieldJSON($query) {
        return response()->json($query->get());
    }
}
