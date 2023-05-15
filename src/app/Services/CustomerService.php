<?php

namespace App\Services;
use App\Models\Customer;
use App\Models\CustomerImage;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerService{
    private $customer;

    private $storage_static_files = "public/images/customers";

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function imageUploader($file)
    {
        $imageInfo = @getimagesize($file);

        if ($imageInfo && ($imageInfo[2] == IMAGETYPE_JPEG || $imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF)) {
            $hashedImage = Str::random(10) . '.' . $file->getClientOriginalExtension();

            $imageData = [
                'path' => config('app.url') . '/storage/images/customers/',
                'name' => $hashedImage,
            ];

            if (!Storage::exists($this->storage_static_files)) {
                Storage::makeDirectory($this->storage_static_files);
            }

            if ($imageData) {
                $image = new CustomerImage($imageData);
                $savedImage = $this->customer->image()->save($image);
                if ($savedImage) {
                    $storedFile = Storage::putFileAs($this->storage_static_files, $file, $hashedImage);
                }
                return $image;
            } else {
                return false;
            }
        }
    }

    public function orderQueryBuilder()
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
            'discount_percent',
            'tracking_number',
            'invoice_number'
        ])
            ->with(['product:id,name,total_price', 'package:id,package_name'])
            ->where('customer_id', $this->customer->id);
    
        return $order;
    }

    public function getOrders()
    {
        $statusNames = config('statuses.order_statuses');
        $orderQ = $this->orderQueryBuilder();
        $orders = $orderQ->get();
        $products = [];
        $products['customer_name'] = $this->customer->name;
        $products['customer_id'] = $this->customer->id;

        foreach ($orders as $key => $order) {

            $product = $order->product;
            $singlePrice = $order->single_sold_price;
            $totalSoldPrice = $order->total_sold_price;
            $soldQuantity = $order->sold_quantity;
            $totalMarkup = $totalSoldPrice - $product->total_price;
            $singleMarkup = $singlePrice - $product->price;
            $regularPrice = $totalSoldPrice / (1 - ($order->discount_percent / 100));
    
            $products['data'][] = [
                'id' => $order->id,
                'name' => $product->name,
                'product_price' => $product->total_price,
                'single_sold_price' => $singlePrice,
                'total_sold_price' => $totalSoldPrice,
                'sold_quantity' => $soldQuantity,
                'total_markup' => number_format($totalMarkup, 2, '.', ''),
                'single_markup' => number_format($singleMarkup, 2, '.', ''),
                'discount' => $order->discount_percent,
                'status' => array_key_exists($order->status, $statusNames) ? $statusNames[$order->status] : $order->status,
                'regular_price' => $regularPrice ? number_format($regularPrice, 2, '.', '') : 0,
                'date_of_sale' => $order->date_of_sale,
                'is_paid' => $order->is_paid,
                'tracking_number' => $order->tracking_number,
                'invoice_number' => $order->invoice_number
            ];
        }
    
        return $products;
    }

}

?>