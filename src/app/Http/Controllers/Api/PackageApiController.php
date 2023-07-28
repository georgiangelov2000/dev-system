<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageApiController extends Controller
{
    public function getData(Request $request) {
        $package = isset($request->package) ? $request->package : null;
        $delivery = isset($request->delivery) ? $request->delivery : null;
        $customer = isset($request->customer) ? $request->customer : null;
        $delivery_date = isset($request->delivery_date) ? $request->delivery_date : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $search = isset($request->search) ? $request->search : null;
        $select_json = isset($request->select_json) ? $request->select_json : null;
        $no_paid_orders = isset($request->no_paid_orders) ? $request->no_paid_orders : null;

        $offset = $request->input('start', 0);
        $packageQuery = Package::query();
        
        if($limit) {
            $packageQuery->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $packageQuery->orderBy($column_name, $order_dir);
        }
        if ($search) {
            $packageQuery->where('package_name', 'LIKE', '%' . $search . '%');
        }
        if($package) {
            $packageQuery->where('package_type',$package);
        }
        if($delivery) {
            $packageQuery->where('delivery_method',$delivery);
        }
        if($customer) {
            $packageQuery->whereHas('orders', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            });
        }
        if($delivery_date) {
            $dates = explode(" - ", $delivery_date);
            $date1_formatted = date('Y-m-d 23:59:59', strtotime($dates[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($dates[1]));

            $packageQuery
                ->where('expected_delivery_date', '>=', $date1_formatted)
                ->where('expected_delivery_date', '<=', $date2_formatted);
        }
        if($no_paid_orders) {
            $packageQuery->whereHas('orders', function ($query) {
                $query->whereIn('status',[3,4])->where('is_paid', false);
            });
        }
        if($select_json) {
            return response()->json($packageQuery->get());
        }
        $packageQuery->withCount([
            'orders as paid_orders_count' => function($query) {
                $query->where('status',1)
                ->where('is_paid',1);
            },
            'orders as overdue_orders_count' => function($query) {
                $query->where('status',4)
                ->where('is_paid',1);
            },
            'orders as pending_orders_count' => function($query) {
                $query->where('status',2)
                ->where('is_paid',0);
            },
            'orders as refund_orders_count' => function($query) {
                $query->where('status',5)
                ->where('is_paid',2);
            }
        ])->withCount('orders');
        

        $filteredRecords = $packageQuery->count();
        $result = $packageQuery->skip($offset)->take($limit)->get();
        
        foreach ($result as $key => $package) {
            $package->package_type = array_key_exists($package->package_type, config('statuses.package_types')) ? config('statuses.package_types.' . $package->package_type) : $package->package_type;
            $package->delivery_method = array_key_exists($package->delivery_method, config('statuses.delivery_methods')) ? config('statuses.delivery_methods.' . $package->delivery_method) : $package->delivery_method;
            $package->is_it_delivered = array_key_exists($package->is_it_delivered, config('statuses.is_paid_statuses')) ? config('statuses.is_paid_statuses.' . $package->is_it_delivered) : $package->is_it_delivered;

            $ordersCount = $package->orders_count;

            $paidOrders = ($package->paid_orders_count + $package->overdue_orders_count);

            if($ordersCount > 0) {
                $paidPercentage = ($paidOrders / $ordersCount) * 100;
            } else {
                $paidPercentage = 0;
            }

            $paidPercentage = round($paidPercentage, 2);

            $package->paid_percentage = $paidPercentage;
        }
        
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
