<?php

namespace App\Services;
use App\Models\Customer;
use App\Models\CustomerImage;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;

class CustomerService{
    private $customer;

    private $storage_static_files = "public/images/customers";

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function orderQueryBuilder()
    {
        $order = Order::select([
            'id',
            'customer_id',
            'purchase_id',
            'sold_quantity',
            'single_sold_price',
            'total_sold_price',
            'date_of_sale',
            'status',
            'is_paid',
            'discount_percent',
            'tracking_number',
        ])
            ->with(['purchase:id,name,total_price,price', 'package:id,package_name'])
            ->where('customer_id', $this->customer->id);
    
        return $order;
    }

    public function getOrders()
    {
            $statusNames = config('statuses.order_statuses');
            $orderQ = $this->orderQueryBuilder()
                ->get()
                ->map(function ($item) use ($statusNames) {
                    $singleMarkUp = abs($item->single_sold_price - $item->purchase->price);
                    $item->status =  array_key_exists($item->status, $statusNames) ? $statusNames[$item->status] : $item->status;
                    $item->single_mark_up = number_format($singleMarkUp, 2, '.', '');
                    $item->purchase = $item->purchase->name;
                    return $item;
                })
                ->toArray();
        $result = new stdClass();
        $result->customer_name=$this->customer->name;
        $result->customer_id=$this->customer->id;
        $result->orders = $orderQ;
        return $result;
    }

}

?>