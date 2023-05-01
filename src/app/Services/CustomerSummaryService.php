<?php

namespace App\Services;

use DateTime;
use App\Models\Order;

class CustomerSummaryService
{
    protected $date;
    protected $customer;

    public function __construct($customer, $date)
    {
        $this->customer = $customer;
        $this->date = $date;
    }
    public function customerQueryBuilder()
    {

        $order = Order::with('product')
            ->where('customer_id', $this->customer);

        if (!empty($this->date)) {

            $dates = explode(" - ", $this->date);
            $date1 = new DateTime($dates[0]);
            $date2 = new DateTime($dates[1]);
            $date1_formatted = $date1->format('Y-m-d');
            $date2_formatted = $date2->format('Y-m-d');

            $order->whereBetween('date_of_sale', [
                $date1_formatted,
                $date2_formatted
            ]);
        }

        return $order;
    }
    public function getOrdersCount()
    {
        return $this->customerQueryBuilder()->count();
    }

    public function getTotalSales()
    {
        return $this->customerQueryBuilder()->sum('total_sold_price');
    }

    public function getProductsByStatus()
    {

        $statusNames = config('statuses.order_statuses');
        
        $orderQ = $this->customerQueryBuilder();

        $orders = $orderQ->get();

        $products = [];


        foreach ($orders as $order) {

            $singlePrice = $order->single_sold_price;
            $totalSoldPrice = $order->total_sold_price;
            $soldQuantity = $order->sold_quantity;
            $totalMarkup = $totalSoldPrice - $order->product->total_price;
            $singleMarkup = $singlePrice - $order->product->price;

            if (!isset($products[$order->status])) {
                $products[$order->status] = [
                    'orders_count' => 0,
                    'status_name' => '',
                    'sum' => 0,
                    'products' => []
                ];
            }

            $products[$order->status]['orders_count']++;
            $products[$order->status]['status_name'] = array_key_exists($order->status,$statusNames) ? $statusNames[$order->status] : ''; 
            $products[$order->status]['sum'] += $totalSoldPrice;
            $products[$order->status]['products'][] = [
                'name' => $order->product->name,
                'single_sold_price' => $singlePrice,
                'total_sold_price' => $totalSoldPrice,
                'sold_quantity' => $soldQuantity,
                'total_markup' => number_format($totalMarkup, 2, '.', ''),
                'single_markup' => number_format($singleMarkup, 2, '.', ''),
                'main_product_id' => $order->product->id
            ];
        }

        // Calculate the sum of product prices by status as a float number with two trailing zeros
        foreach ($products as $status => $data) {
            $products[$status]['sum'] = number_format($data['sum'], 2, '.', '');
        }

        return $products;
    }

    public function getSummary()
    {
        $summary = new \stdClass;
        $summary->orders_count = $this->getOrdersCount();
        $summary->date = $this->date;
        $summary->total_sales = number_format($this->getTotalSales(), 2, '.', '');
        $summary->products = $this->getProductsByStatus();
        

        return $summary;
    }
}
