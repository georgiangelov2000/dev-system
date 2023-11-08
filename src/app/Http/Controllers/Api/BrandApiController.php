<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandApiController extends Controller
{

    public function getData(Request $request)
    {
        $select_json = $request->input('select_json');
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $brandQ = Brand::select('id', 'name', 'description', 'image_path');
        
        $this->applyFilters($request, $brandQ);

        if (boolval($select_json)) {
            return $this->applySelectFieldJSON($brandQ);
        }

        $brandQ->withCount('purchases');

        $filteredRecords = $brandQ->count();
        $totalRecords = Brand::count();
        $result = $brandQ->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function applyFilters($request, $query)
    {
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });
        $query->when($request->input('order_dir') && $request->input('order_column'), function ($query) use ($request) {
            return $query->orderBy($request->input('order_column'), $request->input('order_dir'));
        });
        
    }

    private function applySelectFieldJSON($query)
    {
        return response()->json($query->get());
    }
}
