<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogsApiController extends Controller
{
    public function getData(Request $request) {
        $relations = ['user'];
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $search = $request->input('search');

        $query = Log::query();

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('message', 'LIKE', '%' . $search . '%')
                      ->orWhere('action', 'LIKE', '%' . $search . '%');
            });
        }

        $query->with($relations);
    
        $filteredRecords = $query->count();
        $totalRecords = Log::count();
        $result = $query->skip($offset)->take($limit)->get();
    
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
