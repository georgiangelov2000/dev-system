<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryApiController extends Controller
{
    public function getData(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $select_json = $request->input('select_json');
        $subCategoryQ = SubCategory::query();

        $this->applyFilters($request,$subCategoryQ);

        if(boolval($select_json)) {
            return $this->applySelectFieldJSON($subCategoryQ);
        }

        $filteredRecords = $subCategoryQ->count();
        $result = $subCategoryQ->skip($offset)->take($limit)->get();
        $totalRecords = SubCategory::count();
        
        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );

    }
 
    private function applyFilters($request,$query) {
        $query->when($request->input('order_column') && $request->input('order_dir'), function ($query) use ($request) {
            return $query->orderBy($request->input('order_column'), $request->input('order_dir'));
        });
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });
        $query->when($request->input('supplier'), function ($query) use ($request) {
            return $query->whereHas('suppliers', fn ($query) => $query->where('supplier_id', $request->input('supplier_id')));
        });
        $query->when($request->input('category'), function ($query) use ($request) {
            return $query->where('category_id', $request->input('category'));
        });
    }
    private function applySelectFieldJSON($query){
        return response()->json($query->get());
    }
}