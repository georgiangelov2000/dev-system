<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use DateTime;

class OrderApiController extends Controller
{
    public function getData(Request $request)
    {   $customer = $request->customer;
        $status = $request->status;
        $search = $request->search;
        $date_range = $request->date_range;
        $select_json = $request->select_json;
        $order = $request->order_id;
        
        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);

        $orderQuery = Order::query()->with(['customer:id,name','product:id,name']);

        if ($customer) {
            $orderQuery
            ->where('customer_id', $customer);

            if($select_json) {
                $orderQuery->select('id','customer_id','product_id','invoice_number',)
                ->where('is_paid',0)
                ->whereIn('status',[2,3]);
                return response()->json(
                    $orderQuery->get()
                );
            }

        }
        if($order) {
            $orderQuery->where('id',$order);
        }
        if($search) {
            $orderQuery->where('invoice_number', 'LIKE', '%'.$search.'%');
        }
        if($status) {
            $orderQuery->where('status', $status);
        }
        if ($date_range) {
            $date_pieces = explode(' - ',$date_range);
            $start_date = new DateTime($date_pieces[0]);
            $end_date = new DateTime($date_pieces[1]);

            $orderQuery
            ->where('date_of_sale','>=',$start_date)
            ->where('date_of_sale','<=',$end_date);
        }


        $orderQuery->select(
            'id',
            'customer_id',
            'product_id',
            'invoice_number',
            'sold_quantity',
            'single_sold_price',
            'total_sold_price',
            'original_sold_price',
            'discount_percent',
            'date_of_sale',
            'status',
            'is_paid',
            'created_at',
            'updated_at',
        );

        $filteredRecords = $orderQuery->count();
        $result = $orderQuery->skip($offset)->take($limit)->get();
        foreach ($result as $key => $order) {
            $order->status = array_key_exists($order->status, config('statuses.order_statuses')) ? config('statuses.order_statuses.' . $order->status) : $order->status;
            $order->is_paid = array_key_exists($order->is_paid, config('statuses.is_paid_statuses')) ? config('statuses.is_paid_statuses.' . $order->is_paid) : $order->is_paid;
        }
        $totalRecords = Order::count();

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
