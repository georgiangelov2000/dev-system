<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserApiController extends Controller
{
    public function getData(Request $request)
    {
        $relations = ['role'];

        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $select_json = $request->input('select_json');
        
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
            'image_path',
            'pdf_file_path',
            'last_seen'
        )->with($relations);

        $this->applyFilters($request,$userQuery);

        if (boolval($select_json)) {
            return $this->applySelectFieldJSON($userQuery);
        }

        $filteredRecords = $userQuery->count();
        $totalRecords = User::count();
        $result = $userQuery->skip($offset)->take($limit)->get();

        $currentTime = now();
        $result->each(function ($user) use ($currentTime) {
            $user->online = $user->last_seen >= $currentTime->subMinutes(2);
        });
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
            return $query->orderBy($request->order_column, $request->order_dir);
        });
        $query->when($request->input('role_id') , function ($query) use ($request) {
            return $query->where('role_id',$request->role_id);
        });
        $query->when($request->input('search'), function ($query) use ($request) {
            $query->where('username', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $request->search . '%');
        });
    }
}
