<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageApiController extends Controller
{
    public function getData(Request $request)
    {
        $package = isset($request->package) ? $request->package : null;
        $delivery = isset($request->delivery) ? $request->delivery : null;
        $customer = isset($request->customer) ? $request->customer : null;
        $delivery_date = isset($request->delivery_date) ? $request->delivery_date : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $search = isset($request->search) ? $request->search : null;
        $select_json = isset($request->select_json) ? boolval($request->select_json) : null;
        $no_paid_orders = isset($request->no_paid_orders) ? boolval($request->no_paid_orders) : null;
        $is_it_delivered = isset($request->is_it_delivered) ? boolval($request->is_it_delivered) : null;

        $offset = $request->input('start', 0);
        $packageQuery = Package::query();

        if ($limit) {
            $packageQuery->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $packageQuery->orderBy($column_name, $order_dir);
        }
        if ($search) {
            $packageQuery->where('package_name', 'LIKE', '%' . $search . '%');
        }
        if ($is_it_delivered) {
            $packageQuery->where('is_delivered', 1);
        }
        if ($package) {
            $packageQuery->where('package_type', $package);
        }
        if ($delivery) {
            $packageQuery->where('delivery_method', $delivery);
        }
        if ($customer) {
            $packageQuery->whereHas('orders', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            });
        }
        if ($delivery_date) {
            $dates = explode(" - ", $delivery_date);
            $date1_formatted = date('Y-m-d 23:59:59', strtotime($dates[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($dates[1]));

            $packageQuery
                ->where('expected_delivery_date', '>=', $date1_formatted)
                ->where('expected_delivery_date', '<=', $date2_formatted);
        }
        if ($no_paid_orders) {
            $packageQuery->whereHas('orders', function ($query) {
                $query->whereIn('status', [6]);
            });
        }
        if ($select_json) {
            return response()->json($packageQuery->get());
        }

        $packageQuery->withCount([
            'orders as paid_orders_count' => function ($query) {
                $query->whereHas('payment', function ($subquery) {
                    $subquery->where('payment_status', 1);
                });
            },
            'orders as overdue_orders_count' => function ($query) {
                $query->whereHas('payment', function ($subquery) {
                    $subquery->where('payment_status', 4);
                });
            },
        ])->withCount('orders');

        $filteredRecords = $packageQuery->count();
        $result = $packageQuery->skip($offset)->take($limit)->get();

        $totalRecords = Package::count();

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
