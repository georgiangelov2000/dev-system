<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Purchase;
use App\Models\Package;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\PurchaseImage;
use stdClass;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.home', [
            'dashboard' => $this->getStats()
        ]);
    }

    public function getStats()
    {

        $result = new stdClass;

        $companyInformation = Settings::where('type', 1)->first();

        if ($companyInformation) {
            $settings = json_decode($companyInformation->settings, true);
            $result->company = $settings;
        }
        $result->server_information = $this->serverInformation();

        $result->customers = Customer::count();
        $result->drivers = User::where('role_id', 2)->count();
        $result->orders = Order::count();
        $result->purchases = Purchase::count();
        $result->packages = Package::count();
        $result->suppliers = Supplier::count();
        $result->top_selling_products = $this->topSellingProducts();
        $result->top_categories = $this->topData()[0];
        $result->top_suppliers = $this->topData()[1];
        $result->top_customers = $this->topData()[2];

        $res = json_decode(json_encode($result), true);

        return $res;
    }


    private function serverInformation()
    {
        $serverName = request()->server();
        $server = [];

        $server['web_server'] = $serverName['SERVER_SOFTWARE'];
        $server['http_user_agent'] = $serverName['HTTP_USER_AGENT'];
        $server['gateway_interface'] = $serverName['GATEWAY_INTERFACE'];
        $server['server_protocol'] = $serverName['SERVER_PROTOCOL'];
        $server['php_version'] = $serverName['PHP_VERSION'];
        $server['php_url'] = $serverName['PHP_URL'];
        $server['os'] = php_uname('s');
        $server['ar'] = php_uname('m');

        return $server;
    }

    private function topSellingProducts()
    {
        // $allowedStatuses = [1, 2, 3, 4];

        // $query = OrderPayment::with('order:id,purchase_id', 'order.purchase')
        //     ->whereIn('payment_status', $allowedStatuses)
        //     ->whereHas('order', function ($query) use ($allowedStatuses) {
        //         $query->whereIn('status', $allowedStatuses);
        //     })
        //     ->get();

        // $productDetails = [];
        // $productLimit = 6; // Maximum number of products

        // foreach ($query as $orderPayment) {
        //     $productId = $orderPayment->order->product_id;

        //     $productName = $orderPayment->order->purchase->name; // Replace with your actual product name column

        //     if (!isset($productDetails[$productName])) {
        //         $productDetails[$productName] = [
        //             'description' => $orderPayment->order->purchase->description, // Replace with your actual product description column
        //             'quantity' => 0,
        //             'total_price' => 0,
        //         ];
        //     }

        //     $quantity = $orderPayment->quantity;
        //     $totalPrice = $orderPayment->price;

        //     $productDetails[$productName]['quantity'] += $quantity;
        //     $productDetails[$productName]['total_price'] += $totalPrice;
        //     $productDetails[$productName]['formatted_total_price'] = number_format($productDetails[$productName]['total_price'], 2, '.', ',');

        //     // Check if the product limit is reached
        //     if (count($productDetails) >= $productLimit) {
        //         break;
        //     }
        // }

        // return $productDetails;

        $productLimit = 6; // Maximum number of products

        $productDetails = OrderPayment::join('orders', 'order_payments.order_id', '=', 'orders.id')
            ->join('purchases', 'orders.purchase_id', '=', 'purchases.id')
            ->whereIn('order_payments.payment_status', [1, 2, 3, 4])
            ->whereIn('orders.status', [1, 2, 3, 4])
            ->selectRaw('purchases.id as purchase_id, purchases.name as product_name, purchases.code as product_code,
            SUM(order_payments.quantity) as total_quantity,
            SUM(order_payments.price) as total_price')
            ->groupBy('purchases.id', 'purchases.name', 'purchases.code')
            ->orderByDesc('total_quantity')
            ->limit($productLimit)
            ->get();

        foreach ($productDetails as $product) {
            $image = PurchaseImage::where('purchase_id', $product->purchase_id)->first();
            $product->image = $image->path . '/' . $image->name;
        }

        return $productDetails;
    }

    private function topData()
    {
        $categoryQuery = Category::withCount('products')
            ->withSum('products', 'total_price')
            ->whereHas('products')
            ->orderBy('products_count', 'desc')
            ->limit(4)
            ->get();

        $supplierQuery = Supplier::select('id', 'name', 'email','image_path')
            ->withCount('purchases')
            ->withSum('purchases', 'total_price')
            ->whereHas('purchases')
            ->orderBy('purchases_count', 'desc')
            ->limit(4)
            ->get();

        $customerQuery = Customer::select('id', 'name', 'email','image_path')
            ->withCount('orders')
            ->withSum('orders', 'total_sold_price')
            ->whereHas('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(4)
            ->get();

        return [
            $categoryQuery,
            $supplierQuery,
            $customerQuery
        ];
    }
}
