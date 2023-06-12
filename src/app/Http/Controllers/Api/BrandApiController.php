<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandApiController extends Controller
{

    public function getData(Request $request)
    {

        $select_json = isset($request->select_json) && $request->select_json ? $request->select_json : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $search = isset($request->search) ? $request->search : null;

        $brandQuery = Brand::query()->select('id', 'name', 'description');

            
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        if ($search) {
            $brandQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        if ($column_name && $order_dir) {
            $brandQuery->orderBy($column_name, $order_dir);
        }
        if ($select_json) {
            return response()->json($brandQuery->get());
        }

        $filteredRecords = $brandQuery->count();
        $result = $brandQuery->skip($offset)->take($limit)->get();

        $result->map(function ($brand) {
            $brand->purchases_count = $brand->purchases()->count();
            return $brand;
        });

        $totalRecords = Brand::count();

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
