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
            ->with(['product:id,name,total_price,price', 'package:id,package_name'])
            ->where('customer_id', $this->customer->id);
    
        return $order;
    }

    public function getOrders()
    {
            $statusNames = config('statuses.order_statuses');
            $orderQ = $this->orderQueryBuilder()
                ->get()
                ->map(function ($item) use ($statusNames) {
                    $singleMarkUp = abs($item->single_sold_price - $item->product->price);
                    $item->status =  array_key_exists($item->status, $statusNames) ? $statusNames[$item->status] : $item->status;
                    $item->single_mark_up = number_format($singleMarkUp, 2, '.', '');
                    $item->product = $item->product->name;
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