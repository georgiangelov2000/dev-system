<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerApiController extends Controller
{
    /**
     * Get customers data based on filters and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        // Initialize filter variables
        $country = $request->input('country', null);
        $state = $request->input('state', null);
        $search = $request->input('search', null);

        // Define pagination parameters
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        // Initialize the customer query
        $customerQuery = $this->buildCustomerQuery();

        // Apply search filter
        if ($search) {
            $customerQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply country filter
        if ($country) {
            $this->customerByCountry($country, $customerQuery);

            if ($state) {
                $this->customerByState($country, $state, $customerQuery);
            }
        }

        // Calculate total records and filtered records
        $totalRecords = Customer::count();
        $filteredRecords = $customerQuery->count();

        // Get paginated customer data
        $result = $customerQuery->skip($offset)->take($limit)->get();

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


    private function buildCustomerQuery()
    {
        return Customer::query()->select(
            "id",
            "name",
            "email",
            "phone",
            "address",
            "zip",
            "website",
            "notes",
            "state_id",
            "country_id",
            "image_path"
        )
            ->with(['state:id,country_id,name', 'country:id,name,country_code,short_name'])
            ->withCount([
                'orders as paid_orders_count' => function ($query) {
                    $query->where('status', 1);
                },
                'orders as pending_orders_count' => function ($query) {
                    $query->where('status', 2);
                },
                'orders as overdue_orders_count' => function ($query) {
                    $query->where('status', 4);
                },
                'orders as refund_orders_count' => function ($query) {
                    $query->where('status', 5);
                },
                'orders as ordered_orders_count' => function ($query) {
                    $query->where('status', 6);
                },
            ])->withCount('orders');
    }

    /**
     * Apply a filter to get customers by country.
     *
     * @param string $country
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function customerByCountry($country, $query)
    {
        $query->where('country_id', $country);
    }

    /**
     * Apply a filter to get customers by country and state.
     *
     * @param string $country
     * @param string $state
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function customerByState($country, $state, $query)
    {
        $query
            ->where('country_id', $country)
            ->where('state_id', $state);
    }
}
