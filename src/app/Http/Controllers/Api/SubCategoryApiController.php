<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryApiController extends Controller
{
    public function getData(Request $request)
    {
        $category = isset($request->category) ? $request->category : null;
        $select_json  = isset($request->select_json) ? $request->select_json : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        $search = isset($request->search) ? $request->search : null;
        $offset = $request->input('start', 0);
        
        $subCategoryQ = SubCategory::query();

        if ($category) {
            $subCategoryQ->where('category_id', $category);
        }
        if($limit) {
            $subCategoryQ->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $subCategoryQ->orderBy($column_name, $order_dir);
        }
        if ($search) {
            $subCategoryQ->where('name', 'LIKE', '%' . $search . '%');
        }
        if($select_json) {
            return response()->json(
                $subCategoryQ->get()
            );
        } else {
            $subCategoryQ->with('category')->get();
        }

        $filteredRecords = $subCategoryQ->count();
        $result = $subCategoryQ->get();
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
    
}