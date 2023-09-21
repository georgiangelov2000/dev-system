<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

class StateApiController extends Controller
{
    public function getData(Request $request)
    {
        $countryId = isset($request->country_id) ? $request->country_id : null;
        
        if ($countryId) {
            return response()->json($this->states($countryId));
        }
    }

    private function states($countryId)
    {
        return State::select('id', 'name')->where('country_id', $countryId)->get();
    }
}
