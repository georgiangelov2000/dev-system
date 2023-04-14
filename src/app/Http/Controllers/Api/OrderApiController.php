<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use DateTime;

class OrderApiController extends Controller
{
    public function getData(Request $request)
    {
        $orderQuery = $this->buildOrderQuery();
        $customer = $request->customer;
        $start_date = $request->start_date ? new DateTime($request->start_date) : false;
        $end_date = $request->end_date ? new DateTime($request->end_date) : false;
        $status = $request->status;

        if ($customer) {
            $orderQuery->where('customer_id', $customer);
        }
        if ($start_date && $end_date) {
            $this->filterByDateOfSale(
                $orderQuery,
                $start_date->format('Y-m-d'),
                $end_date->format('Y-m-d')
            );
        }
        if($status) {
            $orderQuery->where('status', $status);
        }


        $result = $this->getOrders($orderQuery);

        return response()->json(['data' => $result]);
    }

    private function filterByDateOfSale($query, $start_date, $end_date)
    {
        return $query->whereBetween('date_of_sale', [
            $start_date,
            $end_date
        ]);
    }

    private function buildOrderQuery()
    {
        return Order::query()->select(
            'id',
            'customer_id',
            'product_id',
            'invoice_number',
            'sold_quantity',
            'single_sold_price',
            'total_sold_price',
            'discount_percent',
            'date_of_sale',
            'status'
        )->with('customer:id,name', 'product:id,name');
    }
    private function getOrders($query)
    {
        return $query->get();
    }
}
