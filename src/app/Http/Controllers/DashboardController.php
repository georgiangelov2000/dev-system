<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Package;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    public function index()
    {
        $data = $this->getData();

        return view('dashboard.home', [
            'dashboard' => $this->getStats($data)
        ]);
    }

    public function getStats(array $data)
    {
        $result = new stdClass;

        $result->company = $data['company'];
        $result->server_information = $this->getServerInformation();
        $result->customers = $data['customers'];
        $result->drivers = $data['drivers'];
        $result->orders = $data['orders'];
        $result->purchases = $data['purchases'];
        $result->packages = $data['packages'];
        $result->suppliers = $data['suppliers'];
        $result->top_selling_products = $data['top_selling_products'];
        $result->top_categories = $data['top_categories'];
        $result->top_suppliers = $data['top_suppliers'];
        $result->top_customers = $data['top_customers'];
        $result->top_selling_drivers = $data['top_selling_drivers'];

        return json_decode(json_encode($result), true);
    }

    private function getData()
    {
        $paidStatuses = [1, 3, 4];

        $customersCount = Customer::count();
        $driversCount = User::where('role_id', 2)->count();
        $ordersCount = Order::count();
        $purchasesCount = Purchase::count();
        $packagesCount = Package::count();
        $suppliersCount = Supplier::count();

        $purchasesQuery = $this->getPurchaseQuery($paidStatuses);
        $categoryQuery = $this->getTopCategoriesQuery();
        $suppliersQuery = $this->getTopSuppliersQuery($paidStatuses);
        $customersQuery = $this->getTopCustomerQuery($paidStatuses);
        $userQuery = $this->getTopSellingDrivers($paidStatuses);

        return [
            'company' => $this->companyInformation(),
            'customers' => $customersCount,
            'drivers' => $driversCount,
            'orders' => $ordersCount,
            'purchases' => $purchasesCount,
            'packages' => $packagesCount,
            'suppliers' => $suppliersCount,
            'top_selling_products' => $purchasesQuery,
            'top_categories' => $categoryQuery,
            'top_suppliers' => $suppliersQuery,
            'top_customers' => $customersQuery,
            'top_selling_drivers' => $userQuery
        ];
    }

    private function getPurchaseQuery(array $paidStatuses)
    {
        return Purchase::select('id', 'name', 'code')
            ->addSelect(DB::raw("(SELECT SUM(payments.price) FROM order_payments AS payments 
                             WHERE payments.order_id IN 
                                 (SELECT id FROM orders 
                                  WHERE orders.purchase_id = purchases.id 
                                  AND orders.status IN (" . implode(',', $paidStatuses) . ")
                                 ) 
                                 AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")
                            ) AS total_price"))
            ->addSelect(DB::raw("(SELECT SUM(payments.quantity) FROM order_payments AS payments 
                            WHERE payments.order_id IN 
                                (SELECT id FROM orders 
                                 WHERE orders.purchase_id = purchases.id 
                                 AND orders.status IN (" . implode(',', $paidStatuses) . ")
                                ) 
                                AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")
                           ) AS total_quantity"))
            ->addSelect(DB::raw("(SELECT CONCAT(purchases_images.path, '/', purchases_images.name) FROM purchases_images 
                            WHERE purchases_images.purchase_id = purchases.id 
                            ORDER BY purchases_images.id 
                            LIMIT 1) AS first_image"))
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM orders AS p
                            WHERE p.purchase_id = purchases.id 
                            AND p.status IN (" . implode(',', $paidStatuses) . ")) AS orders_count"))
            ->whereHas('orders', function ($query) use ($paidStatuses) {
                $query->whereIn('status', $paidStatuses);
            })
            ->take(4)
            ->get();
    }

    private function getTopCategoriesQuery()
    {
        return Category::withCount('products')
            ->withSum('products', 'total_price')
            ->whereHas('products')
            ->orderBy('products_count', 'desc')
            ->limit(4)
            ->get();
    }

    private function getTopSuppliersQuery(array $paidStatuses)
    {
        return Supplier::select('id', 'name', 'image_path')
            ->addSelect(DB::raw("(SELECT SUM(payments.price) FROM purchases AS p
                            JOIN purchase_payments AS payments ON p.id = payments.purchase_id
                            WHERE p.supplier_id = suppliers.id 
                            AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_price"))
            ->addSelect(DB::raw("(SELECT SUM(payments.quantity) FROM purchases AS p
                            JOIN purchase_payments AS payments ON p.id = payments.purchase_id
                            WHERE p.supplier_id = suppliers.id 
                            AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_quantity"))
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM purchases AS p
                            WHERE p.supplier_id = suppliers.id 
                            AND p.status IN (" . implode(',', $paidStatuses) . ")) AS purchases_count"))
            ->whereHas('purchases', function ($query) use ($paidStatuses) {
                $query->whereIn('status', $paidStatuses);
            })
            ->take(4)
            ->get();
    }

    private function getTopCustomerQuery(array $paidStatuses)
    {
        return Customer::select('id', 'name', 'image_path')
            ->addSelect(DB::raw("(SELECT SUM(payments.price) FROM orders AS p
                        JOIN order_payments AS payments ON p.id = payments.order_id
                        WHERE p.customer_id = customers.id 
                        AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_price"))
            ->addSelect(DB::raw("(SELECT SUM(payments.quantity) FROM orders AS p
                        JOIN order_payments AS payments ON p.id = payments.order_id
                        WHERE p.customer_id = customers.id 
                        AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_quantity"))
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM orders AS p
                        WHERE p.customer_id = customers.id 
                        AND p.status IN (" . implode(',', $paidStatuses) . ")) AS orders_count"))
            ->whereHas('orders', function ($query) use ($paidStatuses) {
                $query->whereIn('status', $paidStatuses);
            })
            ->take(4)
            ->get();
    }

    private function getTopSellingDrivers(array $paidStatuses)
    {
        $userQuery = User::select('id', 'role_id', 'username', 'image','phone')
            ->where('role_id', 2)
            ->addSelect(DB::raw("(SELECT SUM(payments.price) FROM orders AS p
                        JOIN order_payments AS payments ON p.id = payments.order_id
                        WHERE p.user_id = users.id 
                        AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_price"))
            ->addSelect(DB::raw("(SELECT SUM(payments.quantity) FROM orders AS p
                        JOIN order_payments AS payments ON p.id = payments.order_id
                        WHERE p.user_id = users.id 
                        AND payments.payment_status IN (" . implode(',', $paidStatuses) . ")) AS total_quantity"))
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM orders AS p
                        WHERE p.user_id = users.id 
                        AND p.status IN (" . implode(',', $paidStatuses) . ")) AS orders_count"))
            ->whereHas('orders', function ($query) use ($paidStatuses) {
                $query->whereIn('status', $paidStatuses);
            })
            ->orderBy('total_price', 'DESC')
            ->orderBy('total_quantity', 'DESC')
            ->orderBy('orders_count', 'DESC')
            ->take(4)
            ->get();

        return $userQuery;
    }

    private function companyInformation()
    {
        $companyInformation = Settings::where('type', 1)->first();

        if ($companyInformation) {
            $settings = json_decode($companyInformation->settings, true);
        }

        return $settings;
    }

    private function getServerInformation()
    {
        $serverName = request()->server();
        $server = [
            'web_server' => $serverName['SERVER_SOFTWARE'],
            'http_user_agent' => $serverName['HTTP_USER_AGENT'],
            'gateway_interface' => $serverName['GATEWAY_INTERFACE'],
            'server_protocol' => $serverName['SERVER_PROTOCOL'],
            'php_version' => $serverName['PHP_VERSION'],
            'php_url' => $serverName['PHP_URL'],
            'os' => php_uname('s'),
            'ar' => php_uname('m'),
        ];

        return $server;
    }
}
