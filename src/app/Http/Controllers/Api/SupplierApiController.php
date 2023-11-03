<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierApiController extends Controller
{

    /**
     * Get supplier data based on filters and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        // Initialize filter variables
        $country = $request->input('country', null);
        $state = $request->input('state', null);
        $category = $request->input('category', null);
        $search = $request->input('search', null);
        $select_json = $request->input('select_json', false);

        // Define pagination parameters
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        // Initialize the supplier query
        $supplierQuery = Supplier::query();

        // Apply search filter
        if ($search) {
            $supplierQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply country and state filters
        if ($country) {
            $this->applyCountryFilter($country, $supplierQuery);

            if ($state) {
                $this->applyStateFilter($country, $state, $supplierQuery);
            }
        }

        // Apply category filter
        if ($category) {
            $this->applyCategoryFilter($category, $supplierQuery);
        }

        // Include suppliers with associated orders
        if ($request->input('with_orders', false)) {
            $supplierQuery->has('purchases');
        }

        // Return JSON response with selected data fields
        if ($select_json) {
            return response()->json(
                $supplierQuery->select('id', 'name')->get()
            );
        }

        // Include relationships and counts
        $supplierQuery->with([
            'state:id,name',
            'country:id,name,short_name',
            'categories:id,name'
        ])->select([
            'id',
            'name',
            'email',
            'phone',
            'address',
            'zip',
            'website',
            'notes',
            'state_id',
            'country_id',
            'image_path'
        ])
        ->withCount(
            [
                'purchases as paid' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 1);
                    });
                },
                'purchases as pending' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 2);
                    });
                },
                'purchases as overdue' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 4);
                    });
                },
                'purchases as refunded' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 5);
                    });
                },
            ]
        )
        ->withCount('purchases');

        // Calculate total records and filtered records
        $totalRecords = Supplier::count();
        $filteredRecords = $supplierQuery->count();

        // Get paginated supplier data
        $result = $supplierQuery->skip($offset)->take($limit)->get();

        // Return JSON response with data for DataTables
        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    /**
     * Apply a filter to get suppliers by country.
     *
     * @param string $country
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyCountryFilter($country, $query)
    {
        $query->where('country_id', $country);
    }

    /**
     * Apply a filter to get suppliers by country and state.
     *
     * @param string $country
     * @param string $state
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyStateFilter($country, $state, $query)
    {
        $query
            ->where('country_id', $country)
            ->where('state_id', $state);
    }

    /**
     * Apply a filter to get suppliers by category.
     *
     * @param array $category
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyCategoryFilter($category, $query)
    {
        $query->whereHas('categories', function ($query) use ($category) {
            $query->whereIn('categories.id', $category);
        });
    }
}
