<?php

namespace App\Services;

use App\Helpers\DashboardHelper;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use stdClass;

class DashboardService
{

    public $currentMonthStart;
    public $currentMonthEnd;
    public $previousMonthStart;
    public $previousMonthEnd;

    public function __construct()
    {
        $this->currentMonthStart = Carbon::now()->startOfMonth();
        $this->currentMonthEnd = Carbon::now()->endOfMonth();
        $this->previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $this->previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
    }

    //Query Builder
    private function orderQueryBuilder()
    {
        return Order::query()->select(['id', 'date_of_sale', 'is_paid', 'status', 'customer_id', 'single_sold_price', 'total_sold_price']);
    }
    private function productQueryBuilder()
    {
        return Product::query()
            ->select(['id', 'created_at','is_paid','total_price'])
            ->where('status', 'enabled');
    }

    // Logic
    public function orders()
    {
        $statusNames = config('statuses.order_statuses');

        $curMonthOrderCounts = DashboardHelper::getCounts(
            $this->orderQueryBuilder()->whereBetween('date_of_sale',[
                $this->currentMonthStart, $this->currentMonthEnd
            ])
        );

        $prevMonthOrderCounts = DashboardHelper::getCounts(
            $this->orderQueryBuilder()->whereBetween('date_of_sale',[
                $this->previousMonthStart, $this->previousMonthEnd
            ])
        );

        $curMontByStatus = DashboardHelper::getCountsByStatus(
            $this->orderQueryBuilder()->whereBetween('date_of_sale',[
                $this->currentMonthStart, $this->currentMonthEnd
            ]),$statusNames,'status'
        );

        $prevMontByStatus = DashboardHelper::getCountsByStatus(
            $this->orderQueryBuilder()->whereBetween('date_of_sale',[
                $this->previousMonthStart, $this->previousMonthEnd
            ]),$statusNames,'status'
        );

        return [
            'this_month' => [
                'counts' => $curMonthOrderCounts,
                'by_status' => $curMontByStatus
            ],
            'previous_month' => [
                'counts' => $prevMonthOrderCounts,
                'by_status' => $prevMontByStatus
            ]
        ];
    }
    public function packages()
    {
        $deliveryMethods = config('statuses.delievery_methods');

        $packageBuilder = $this->orderQueryBuilder()
            ->where('package_id', '>', 0)
            ->select('package_id')
            ->with('package:id,delievery_method');

        $thisMonthCountGroupedData = DashboardHelper::getCountsByPackage(
            $packageBuilder->whereBetween('date_of_sale', 
                [$this->currentMonthStart, $this->currentMonthEnd]
            ),$deliveryMethods
        );

        $prevMonthCountGroupedData = DashboardHelper::getCountsByPackage(
            $packageBuilder->whereBetween('date_of_sale', 
                [$this->previousMonthStart, $this->previousMonthEnd]
            ),$deliveryMethods
        );

        return [
           "this_month" => $thisMonthCountGroupedData,
           'previous_month' => $prevMonthCountGroupedData
        ];
    }
    public function products()
    {

        $thisMonthPackageCounts = $this->productQueryBuilder()
            ->whereBetween('created_at', [$this->currentMonthStart, $this->currentMonthEnd])
            ->count();

        $previousMonthCounts = $this->productQueryBuilder()
            ->whereBetween('created_at', [$this->previousMonthStart, $this->previousMonthEnd])
            ->count();

        return [
            'this_month' => [
                'counts' => $thisMonthPackageCounts
            ],
            'previous_month' => [
                'counts' => $previousMonthCounts
            ]
        ];
    }
    public function topFiveCustomers()
    {
        $customerCounts = $this->orderQueryBuilder()
            ->where('is_paid', 1)
            ->with(['customer:id,name,email,phone', 'customerPayments:id,order_id,price'])
            ->get()
            ->groupBy(function ($query) {
                return $query->customer->name;
            })
            ->map(function ($orders) {
                $totalPrice = $orders->sum(function ($order) {
                    return $order->customerPayments->sum('price');
                });

                return [
                    'customer_id' => $orders->first()->customer->id,
                    'customer_email' => $orders->first()->customer->email,
                    'customer_phone' => $orders->first()->customer->phone,
                    'orders_count' => $orders->count(),
                    'total_price' => $totalPrice,
                ];
            })
            ->sortByDesc('orders_count')
            ->take(5)
            ->toArray();


        return $customerCounts;
    }
    public function orderSumByStatus()
    {
        $statusNames = config('statuses.order_statuses');

        $result = $this->orderQueryBuilder()
            ->with(['customerPayments:id,order_id,price'])
            ->get()
            ->groupBy('status')
            ->map(function ($orders) {
                $sum = $orders->reduce(function ($carry, $order) {
                    if ($order->customerPayments->isNotEmpty()) {
                        $paymentSum = $order->customerPayments->sum('price');
                        $carry += $paymentSum;
                    } else {
                        $carry += $order->total_sold_price;
                    }
                    return $carry;
                }, 0);
                return $sum;
            })
            ->mapWithKeys(function ($item, $status) use ($statusNames) {
                return [$statusNames[$status] => $item];    
            })->toArray();

        return $result;
    }
    public function productSumByStatus(){
        $result = $this->productQueryBuilder()
        ->get()
        ->groupBy('is_paid')
        ->map(function ($products){
            $sum = $products->reduce(function ($carry, $product) {
                if ($product->is_paid) {
                    $paymentSum = $product->total_price;
                    $carry += $paymentSum;
                } else {
                    $carry += $product->total_price;
                }
                return $carry;
            }, 0);
            return $sum;
        })
        ->mapWithKeys(function ($item, $status){
            $name = $status ? 'paid' : 'not_paid';        
            return [
                $name => $item,
            ];    
        })->toArray();

        
        $result['total'] = array_sum($result);

        return $result;
    }

    public function getData()
    {
        $result = new stdClass();
        $result->orders = $this->orders();
        $result->packages = $this->packages();
        $result->products = $this->products();
        $result->top_five_customers = $this->topFiveCustomers();
        $result->grouped_orders_sum = $this->orderSumByStatus();
        $result->grouped_products_sum = $this->productSumByStatus();
        return (array) $result;
    }
}
