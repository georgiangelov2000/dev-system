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
        $select_json = $request->input('select_json');
        $relations = ['state:id,country_id,name', 'country:id,name,country_code,short_name'];
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $customerQ = Customer::query()->select(
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
        );

        $this->applyFilters($request,$customerQ);

        if (boolval($select_json)) {
            return $this->applySelectFieldJSON($customerQ);
        }

        $customerQ->with($relations)->withCount([
                'orders as paid_orders_count' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 1);
                    });
                },
                'orders as pending_orders_count' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 2);
                    });
                },
                'orders as overdue_orders_count' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 4);
                    });
                },
                'orders as refund_orders_count' => function ($query) {
                    $query->whereHas('payment', function ($subquery) {
                        $subquery->where('payment_status', 5);
                    });
                },
            ])->withCount('orders');


        $filteredRecords = $customerQ->count();
        $totalRecords = Customer::count();
        $result = $customerQ->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function applyFilters($request,$query){
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });
        $query->when($request->input('country'), function ($query) use ($request) {
            return $query->where('country_id', $request->input('country'))
            ->when($request->input('state'), function ($query) use ($request) {
                return $query->where('state_id', $request->input('state'));
            });
        });
    }

    private function applySelectFieldJSON($query){
        return response()->json($query->get());
    }
}
