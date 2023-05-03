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

        $order = Order::with(['product', 'package:id,package_name'])
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
            $package = $order->package ? $order->package->package_name : 'No Package';

            if (!isset($products[$package])) {
                $products[$package] = [
                    'orders_count' => 0,
                    'sum' => 0,
                    'status' => []
                ];
            }
            $products[$package]['orders_count']++;
            $products[$package]['sum'] += $totalSoldPrice;
            $products[$package]['sum'] = number_format($products[$package]['sum'], 2, '.', ''); // add this line to format the number

            if (!isset($products[$package]['status'][$statusNames[$order->status] ?? ''])) {
                $products[$package]['status'][$statusNames[$order->status] ?? ''] = [
                    'orders_count' => 0,
                    'sum' => 0,
                    'products' => []
                ];
            }

            $products[$package]['status'][$statusNames[$order->status] ?? '']['orders_count']++;
            $products[$package]['status'][$statusNames[$order->status] ?? '']['sum'] += $totalSoldPrice;
            $products[$package]['status'][$statusNames[$order->status] ?? '']['sum'] = number_format($products[$package]['status'][$statusNames[$order->status] ?? '']['sum'], 2, '.', ''); // add this line to format the number
            $products[$package]['status'][$statusNames[$order->status] ?? '']['products'][] = [
                'id' => $order->id,
                'name' => $order->product->name,
                'single_sold_price' => $singlePrice,
                'total_sold_price' => $totalSoldPrice,
                'sold_quantity' => $soldQuantity,
                'total_markup' => number_format($totalMarkup, 2, '.', ''),
                'single_markup' => number_format($singleMarkup, 2, '.', ''),
                'main_product_id' => $order->product->id
            ];
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
