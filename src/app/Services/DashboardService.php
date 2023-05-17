<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

use stdClass;

class DashboardService{

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
    private function orderQueryBuilder(){
        return Order::query()->select(['id','date_of_sale','is_paid','status','customer_id','single_sold_price','total_sold_price']);
    }
    private function productQueryBuilder(){
        return Product::query()
        ->select(['id','created_at'])
        ->where('status','enabled');
    }

    // Logic
    public function orders(){

        $statusNames = config('statuses.order_statuses');

        $currentMonthOrdersCount = $this->orderQueryBuilder()
            ->whereBetween('date_of_sale', [$this->currentMonthStart, $this->currentMonthEnd
        ])->count();

        $previousMonthOrdersCount = $this->orderQueryBuilder()
            ->whereBetween('date_of_sale', [$this->previousMonthStart, $this->previousMonthEnd
        ])->count();

        $currentMonthOrderByStatus = $this->orderQueryBuilder()
        ->whereBetween('date_of_sale', [$this->currentMonthStart, $this->currentMonthEnd])
        ->get()
        ->groupBy('status')
        ->map(function ($query) {
            return ['count' => $query->count()];
        })
        ->mapWithKeys(function ($item, $status) use ($statusNames) {
            return [$statusNames[$status] => $item];
        })->toArray();


        $previousMonthOrders = $this->orderQueryBuilder()
        ->whereBetween('date_of_sale', [$this->previousMonthStart, $this->previousMonthEnd])
        ->get()
        ->groupBy('status')
        ->map(function ($query) {
            return ['count' => $query->count()];
        })
        ->mapWithKeys(function ($item, $status) use ($statusNames) {
            return [$statusNames[$status] => $item];
        })->toArray();


        return [
            'this_month' => [
                'counts' => $currentMonthOrdersCount,
                'by_status' => $currentMonthOrderByStatus
            ],
            'previous_month' => [
                'counts' => $previousMonthOrdersCount,
                'by_status' => $previousMonthOrders
            ],
        ];
    }
    public function packages(){
        $deliveryMethods = config('statuses.delievery_methods');

        $packageBuilder = $this->orderQueryBuilder()
        ->where('package_id','>',0)
        ->select('package_id')
        ->with('package:id,delievery_method');

        $thisMonthCount =0;
        $previousMonthCount=0;

        $thisMonthPackages = $packageBuilder
        ->whereBetween('date_of_sale', [$this->currentMonthStart, $this->currentMonthEnd])
        ->get()
        ->groupBy(function ($packageOrder) {
            return optional($packageOrder->package)->delievery_method;
        })
        ->mapWithKeys(function ($group, $key) use ($deliveryMethods,&$thisMonthCount) {
            $count = count($group);
            $thisMonthCount += $count;
            return [$deliveryMethods[$key] => $group->count()];
        });

        $previousMonthPackages = $packageBuilder
        ->whereBetween('date_of_sale', [$this->previousMonthStart, $this->previousMonthEnd])
        ->get()
        ->groupBy(function ($packageOrder) {
            return optional($packageOrder->package)->delievery_method;
        })
        ->mapWithKeys(function ($group, $key) use ($deliveryMethods,&$previousMonthCount) {
            $count = count($group);
            $previousMonthCount += $count;
            return [$deliveryMethods[$key] => $group->count()];
        });    

        return [
            'this_month' => [
                'counts' => $thisMonthCount,
                'by_status' => $thisMonthPackages
            ],
            'previous_month' => [
                'counts' => $previousMonthCount,
                'by_status' => $previousMonthPackages
            ],
        ];
    }
    public function products(){
        
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
    public function topFiveCustomers(){

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
        ->take(5);

        return $customerCounts;
    }

    public function getData(){
        $result = new stdClass();
        $result->orders = $this->orders();
        $result->packages = $this->packages();
        $result->products = $this->products();
        $result->top_five_customers = $this->topFiveCustomers();

        return (array) $result;
    }

}
