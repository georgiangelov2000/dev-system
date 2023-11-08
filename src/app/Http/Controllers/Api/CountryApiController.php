<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryApiController extends Controller
{
    public function getData(Request $request)
    {
        $select_json = $request->input('select_json');
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $countryQ = Country::select('id', 'name', 'short_name', 'country_code');

        $this->applyFilters($request,$countryQ);
        
        if(boolval($select_json)) {
           return $this->applySelectFieldJSON($countryQ);
        }

        $countryQ->withCount(['customers','suppliers']);

        $totalRecords = Country::count();
        $filteredRecords = $countryQ->count();
        $result = $countryQ->skip($offset)->take($limit)->get();

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
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });
    }
    
    private function applySelectFieldJSON($query){
        return response()->json($query->get());
    }
}
