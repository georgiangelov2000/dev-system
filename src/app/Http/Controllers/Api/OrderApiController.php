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

        $select_json = $request->input('select_json');
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $orderQuery = Order::query();

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

        $this->applyFilters($request, $orderQuery);

        if ($select_json) {
            return $this->applySelectFieldJSON($orderQuery->with('purchase'));
        }

        $orderQuery->with($relations);

        $filteredRecords = $orderQuery->count();
        $totalRecords = Order::count();
        $result = $orderQuery->skip($offset)->take($limit)->get();
        
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
        $query->when($request->input('order_column') && $request->input('order_dir'), function ($query) use ($request) {
            return $query->orderBy($request->input('order_column'), $request->input('order_dir'));
        });
        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('tracking_number', 'LIKE', '%' . $request->input('search') . '%');
        });
        $query->when($request->input('id'), function ($query) use ($request) {
            return $query->where('id', $request->input('id'));
        });
        $query->when($request->input('customer'), function ($query) use ($request) {
            return $query->where('customer_id', $request->input('customer'));
        });
        $query->when($request->input('user_id'), function ($query) use ($request) {
            return $query->where('user_id', $request->input('user_id'));
        });
        $query->when($request->input('package'), function ($query) use ($request) {
            return $query->where('package_id', $request->input('package'));
        });
        $query->when($request->input('status'), function ($query) use ($request) {
            $statuses = $request->input('status');
            return $query->whereHas('payment', fn ($query) => $query->whereIn('payment_status', $statuses));
        });
        $query->when($request->input('expected_delivery_date'), function ($query) use ($request) {
            $date_pieces = explode(' - ', $request->input('expected_delivery_date'));
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));
            return $query
            ->where('expected_delivery_date', '>=', $date1_formatted)
            ->where('expected_delivery_date', '<=', $date2_formatted);
        });
        $query->when($request->input('delivery_date'), function ($query) use ($request) {
            $date_pieces = explode(' - ', $request->input('delivery_date'));
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));
            return $query
            ->where('delivery_date', '>=', $date1_formatted)
            ->where('delivery_date', '<=', $date2_formatted);
        });
        $query->when($request->input('expected_date_of_payment'), function ($query) use ($request) {
            $date_pieces = explode(' - ', $request->input('expected_date_of_payment'));
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));
            
            return $query->whereHas('payment', function ($query) use ($date1_formatted, $date2_formatted) {
                $query
                    ->where('expected_date_of_payment', '>=', $date1_formatted)
                    ->where('expected_date_of_payment', '<=', $date2_formatted);
            });
        });
        $query->when($request->input('without_package'), function ($query) use ($request) {
            $withoutPackage = $request->input('without_package');
            return $query->whereNull('package_id');
        });
    }

    private function applySelectFieldJSON($query){
        return response()->json(["order" => $query->first()]);
    }
}
