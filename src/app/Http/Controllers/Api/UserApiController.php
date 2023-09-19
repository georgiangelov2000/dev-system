<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserApiController extends Controller
{
    public function getData(Request $request)
    {
        $relations = ['role'];

        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $search = isset($request->search) && $request->search ? $request->search : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $role_id = isset($request->role_id) ? $request->role_id : null;
        $no_datatable_draw = isset($request->no_datatable_draw) ? boolval($request->no_datatable_draw) : null;

        $userQuery = User::select(
            'id',
            'email',
            'role_id',
            'username',
            'first_name',
            'middle_name',
            'last_name',
            'password',
            'card_id',
            'birth_date',
            'gender',
            'phone',
            'address',
            'photo',
            'pdf_file_path'
        )->with($relations);

        if ($search) {
            $userQuery->where(function ($query) use ($search) {
                $query->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($column_name && $order_dir) {
            $userQuery->orderBy($column_name, $order_dir);
        }
        if ($role_id) {
            $userQuery->where('role_id', $role_id);
        }
        if ($no_datatable_draw) {
            return response()->json($userQuery->get());
        }

        $result = $userQuery->skip($offset)->take($limit)->get();
        $totalRecords = User::count();
        $filteredRecords = $userQuery->count();

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
