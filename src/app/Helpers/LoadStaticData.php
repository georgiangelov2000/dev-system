<?php

namespace App\Helpers;

use App\Models\SubCategory;
use Illuminate\Support\Facades\Cache;
use App\Models\State;
use App\Models\Country;
use App\Models\Category;
use App\Models\Supplier;

class LoadStaticData {

    static function loadSubcategories() {

        $unAssignedSubcategories = SubCategory::whereDoesntHave('categories')
                ->select('id', 'name')
                ->get();

        $assignedSubCategories = SubCategory::has('categories')
                ->select('id', 'name')
                ->get();

        return ['unAssignedSubcategories' => $unAssignedSubcategories, 'assignedSubCategories' => $assignedSubCategories];
    }

    static function loadCallStatesAndCountries($country = null) {

        $statesQuery = State::select('id', 'name');

        if ($country) {
            $statesQuery->where('country_id', $country);
        }

        $states = $statesQuery->get();
        $countries = Country::select('id', 'name')->get();

        return [
            'states' => $states,
            'countries' => $countries
        ];
    }

    static function loadCallCategories($option = null) {
        $query = Category::select('id', 'name');

        if ($option == 'unnasigned') {
            $query->whereDoesntHave('suppliers');
        } else {
            $query->get();
        }

        return $query->get();
    }

    static function callSupliers() {
        $query = Supplier::query()->with('categories');

        $suppliers = $query
                ->whereNotNull('country_id')
                ->whereNotNull('state_id')
                ->whereHas('categories')
                ->get();

        return $suppliers;
    }

}
