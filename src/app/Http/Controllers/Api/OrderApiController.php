<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderApiController extends Controller
{
    public function getData(Request $request)
    {
        $relations = [
            'customer:id,name',
            'payment:id,payment_status,order_id,alias,delivery_status',
            'purchase:id,name,image_path,price,quantity,initial_quantity',
            'user:id,username',
            'package:id,package_name'
        ];

        $id = isset($request->id) && $request->id ? $request->id : null;
        $customer = isset($request->customer) && $request->customer ? $request->customer : null;
        $package = isset($request->package) && $request->package ? $request->package : null;
        $statuses = isset($request->status) && $request->status ? $request->status : null;
        $search = isset($request->search) && $request->search ? $request->search : null;
        $delivery_date = isset($request->date_range) && $request->date_range ? $request->date_range : null;
        $date_of_payment = isset($request->date_of_payment) && $request->date_of_payment ? $request->date_of_payment : null;
        $select_json = isset($request->select_json) ? boolval($request->select_json) : null;
        $order = isset($request->order_id) && $request->order_id ? $request->order_id : null;
        $product = isset($request->product_id) && $request->product_id ? $request->product_id : null;
        $withoutPackage = isset($request->without_package) ? boolval($request->without_package) : null;

        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $orderQuery = Order::query()->with($relations);

        $orderQuery->select(
            'id',
            'customer_id',
            'purchase_id',
            'tracking_number',
            'sold_quantity',
            'single_sold_price',
            'discount_single_sold_price',
            'total_sold_price',
            'original_sold_price',
            'discount_percent',
            'expected_delivery_date',
            'package_extension_date',
            'user_id',
            'package_id',
            'delivery_date',
            'created_at',
            'updated_at',
            'is_it_delivered',
        );

        if ($customer) {
            $orderQuery->where('customer_id', $customer);
        }
        if ($id) {
            $orderQuery->where('id', $id);
        }
        if ($package) {
            $orderQuery->where('package_id',$package);
        }
        if ($product) {
            $orderQuery->where('purchase_id', $product);
        }
        if ($order) {
            $orderQuery->where('id', $order);
        }
        if ($search) {
            $orderQuery->where('tracking_number', 'LIKE', '%' . $search . '%');
        }
        if ($statuses) {
            $orderQuery->whereHas('payment', function ($subquery) use ($statuses) {
                $subquery->whereIn('payment_status', $statuses);
            });
        }        
        if ($delivery_date) {
            $date_pieces = explode(' - ', $delivery_date);
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));

            $orderQuery
                ->where('expected_delivery_date', '>=', $date1_formatted)
                ->where('expected_delivery_date', '<=', $date2_formatted);
        }
        if ($date_of_payment) {
            $date_pieces = explode(' - ', $date_of_payment);
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));

            $orderQuery->whereHas('payment', function ($query) use ($date1_formatted, $date2_formatted) {
                $query
                    ->where('date_of_payment', '>=', $date1_formatted)
                    ->where('date_of_payment', '<=', $date2_formatted);
            });
        }
        if ($withoutPackage) {
            $orderQuery->whereNull('package_id');
        }
        if ($select_json) {
            if($orderQuery->count() > 1) {
                return response()->json($orderQuery->get());
            } 
            return response()->json(["order" => $orderQuery->first()]);
        }        

        $result = $orderQuery->skip($offset)->take($limit)->get();

        $totalRecords = Order::count();
        $filteredRecords = $orderQuery->count();

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
