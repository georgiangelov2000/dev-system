<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryApiController extends Controller
{
    public function getData(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $countryQ = Country::select('id', 'name', 'short_name', 'country_code')
        ->withCount(['customers','suppliers']);

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
}
