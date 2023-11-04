<?php

namespace App\Http\Controllers;

use App\Helpers\RedisCacheHelper;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Package;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    private $redisCacheHelper;
    private $payment_statuses;

    public function __construct(RedisCacheHelper $redisCacheHelper)
    {
        $this->redisCacheHelper = $redisCacheHelper;
        $this->payment_statuses = config('statuses.payment_statuses');
    }

    public function index()
    {
        return view('dashboard.home', [
            'dashboard' => $this->getData()
        ]);
    }

    public function getData()
    {
        $result = new stdClass();
        $result->stats = $this->getStats();
        $result->company_information = $this->getCompanyInformation();
        $result->summary = $this->summaryStatistics();
        return $result;
    }

    private function getStats()
    {
        return [
            'orders_count' => Order::count(),
            'purchase_count' => Purchase::count(),
            'suppliers_count' => Supplier::count(),
            'customers_count' => Customer::count(),
            'packages_count' => Package::count(),
            'categories_count' => Category::count(),
            'purchase_payments_count' => PurchasePayment::count(),
            'order_payments_count' => OrderPayment::count(),
        ];
    }

    private function getCompanyInformation()
    {
        // Attempt to fetch the data from Redis cache
        $cachedCompanyInformation = $this->redisCacheHelper->get('company_information');

        // If data is not cached or needs to be updated
        if ($cachedCompanyInformation === null) {
            // Fetch the information from the database
            $companyInformation = Settings::where('type', 1)->first();

            if ($companyInformation) {
                $settings = json_decode($companyInformation->settings, true);

                // Store the information in Redis for future use with a cache expiry of 1 hour (3600 seconds)
                $this->redisCacheHelper->put('company_information', $settings, 3600);
            }
        } else {
            // Use the cached data
            $settings = $cachedCompanyInformation;
        }

        return $settings;
    }

    private function summaryStatistics()
    {

        $paymentStatuses = array_keys($this->payment_statuses);

        $purchasePaymentQuery = PurchasePayment::whereIn('payment_status', $paymentStatuses)
            ->select('payment_status', DB::raw('SUM(price) as total_price'))
            ->groupBy('payment_status')
            ->get();

        $orderPaymentQuery = OrderPayment::whereIn('payment_status', $paymentStatuses)
            ->select('payment_status', DB::raw('SUM(price) as total_price'))
            ->groupBy('payment_status')
            ->get();

        $supplierQuery = Supplier::select('id', 'name', 'image_path')
            ->addSelect(DB::raw("(SELECT COALESCE(REPLACE(FORMAT(SUM(COALESCE(payments.price, 0)), 2), ',', '.'), '0.00') FROM purchases AS p
                        JOIN purchase_payments AS payments ON p.id = payments.purchase_id
                        WHERE p.supplier_id = suppliers.id 
                        AND payments.payment_status IN (" . implode(',', array_keys($this->payment_statuses)) . ")) AS total_price"))
            ->take(5)
            ->get();


        $customerQuery = Customer::select('id', 'name', 'image_path')
        ->addSelect(DB::raw("(SELECT COALESCE(REPLACE(FORMAT(SUM(COALESCE(payments.price, 0)), 2), ',', '.'), '0.00') FROM orders AS p
                    JOIN order_payments AS payments ON p.id = payments.order_id
                    WHERE p.customer_id = customers.id 
                    AND payments.payment_status IN (" . implode(',', array_keys($this->payment_statuses)) . ")) AS total_price"))
        ->take(5)
        ->get();

        return [
            'purchase_payments' => $this->groupedPaymentStatuses($purchasePaymentQuery, $paymentStatuses),
            'order_payments' => $this->groupedPaymentStatuses($orderPaymentQuery, $paymentStatuses),
            'suppliers' => $supplierQuery,
            'customers' => $customerQuery
        ];
    }

    private function groupedPaymentStatuses($query, $statuses)
    {
        return collect($statuses)
            ->map(function ($status) use ($query, $statuses) {
                $result = $query->where('payment_status', $status)->first();
                $totalPrice = $result ? number_format($result->total_price, 2, '.', '.') : number_format(0, 2);
                return [
                    'status' => $this->payment_statuses[$status],
                    'total_price' => $totalPrice,
                ];
            });
    }
}
