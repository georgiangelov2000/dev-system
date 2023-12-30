<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RolesManagementApiController extends Controller
{
    public function getData(Request $request) {
        $relations = ['rolesAccessManagement'];
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $roleQ = Role::query();

        $roleQ->with($relations);

        $totalRecords = Role::count(); 
        $filteredRecords = $roleQ->count();
        $result = $roleQ->skip($offset)->take($limit)->get();

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
