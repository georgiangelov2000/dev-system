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
        $order = Order::select([
            'id',
            'customer_id',
            'product_id',
            'sold_quantity',
            'single_sold_price',
            'total_sold_price',
            'date_of_sale',
            'status',
            'package_id',
            'is_paid',
            'discount_percent'
        ])
            ->with(['product:id,name', 'package:id,package_name'])
            ->where('customer_id', $this->customer);
    
        if (!empty($this->date)) {
            $dates = explode(" - ", $this->date);
            $date1 = new DateTime($dates[0]);
            $date2 = new DateTime($dates[1]);
            $date1_formatted = $date1->format('Y-m-d');
            $date2_formatted = $date2->format('Y-m-d');
    
            $order->when(!empty($this->date), function ($query) use ($date1_formatted, $date2_formatted) {
                return $query->whereBetween('date_of_sale', [
                    $date1_formatted,
                    $date2_formatted
                ]);
            });
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
            $paidSalesTotalPrice = $order->is_paid ? $totalSoldPrice : 0;
            $soldQuantity = $order->sold_quantity;
            $totalMarkup = $totalSoldPrice - $order->product->total_price;
            $singleMarkup = $singlePrice - $order->product->price;
            $package = $order->package ? $order->package->package_name : 'No Package';

            if (!isset($products[$package])) {
                $products[$package] = [
                    'paid_sales_total_price' => 0,
                    'orders_count' => 0,
                    'sum' => 0,
                    'status' => []
                ];
            }
            $products[$package]['orders_count']++;
            $products[$package]['paid_sales_total_price'] += $paidSalesTotalPrice;
            $products[$package]['sum'] += $totalSoldPrice;

            if (!isset($products[$package]['status'][$statusNames[$order->status] ?? ''])) {
                $products[$package]['status'][$statusNames[$order->status] ?? ''] = [
                    'orders_count' => 0,
                    'sum' => 0,
                    'products' => []
                ];
            }

            $products[$package]['status'][$statusNames[$order->status] ?? '']['orders_count']++;
            $products[$package]['status'][$statusNames[$order->status] ?? '']['sum'] += $totalSoldPrice;
            $products[$package]['status'][$statusNames[$order->status] ?? '']['products'][] = [
                'id' => $order->id,
                'name' => $order->product->name,
                'single_sold_price' => $singlePrice,
                'total_sold_price' => $totalSoldPrice,
                'sold_quantity' => $soldQuantity,
                'total_markup' => number_format($totalMarkup, 2, '.', ''),
                'single_markup' => number_format($singleMarkup, 2, '.', ''),
                'main_product_id' => $order->product->id,
                'discount' => $order->discount_percent
            ];
        }

        foreach ($products as &$package) {
            $package['paid_sales_total_price'] = number_format($package['paid_sales_total_price'], 2, '.', '');
            $package['sum'] = number_format($package['sum'], 2, '.', '');
    
            foreach ($package['status'] as &$status) {
                $status['sum'] = number_format($status['sum'], 2, '.', '');
            }
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
