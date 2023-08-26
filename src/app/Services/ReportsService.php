<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use App\Helpers\RedisCacheHelper;
use App\Models\Order;
use App\Models\Purchase;
use stdClass;

class ReportsService
{
    protected $helper;
    protected $controller;

    public function __construct(FunctionsHelper $helper)
    {
        $this->helper = $helper;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    private function orderCalcForPrevMonth($period, $option)
    {
        list($start, $end) = $period;

        $res = [
            'count' => 0,
            'total_price_with_discount' => 0,
            'total_price_without_discount' => 0,
            'quantity' => 0,
            'total_order_payment_price' => 0,
            'total_order_payment_quantity' => 0
        ];

        $orderQPrevMonth = Order::query()
            ->select(
                'id',
                'total_sold_price',
                'sold_quantity',
                'original_sold_price',
                'status'
            )
            ->with(['orderPayments.invoice'])
            ->where('date_of_sale', '>=', $start)
            ->where('date_of_sale', '<=', $end)
            ->where('status', $option);

        $totalOrderPaymentAmount = $orderQPrevMonth->get()->sum(function ($order) {
            return $order->orderPayments->sum('price');
        });

        $totalOrderPaymentQuantity = $orderQPrevMonth->get()->sum(function ($order) {
            return  $order->orderPayments->sum('quantity');
        });

        $res['count'] = $orderQPrevMonth->count();
        $res['total_price_with_discount'] = $orderQPrevMonth->sum('total_sold_price');
        $res['original_price_without_discount'] = $orderQPrevMonth->sum('original_sold_price');
        $res['quantity'] = $orderQPrevMonth->sum('sold_quantity');
        $res['total_order_payment_price'] = number_format($totalOrderPaymentAmount, 2, '.', ',');
        $res['total_order_payment_quantity'] = $totalOrderPaymentQuantity;

        return $res;
    }

    public function generateOrderReport($option, $dates)
    {
        $cacheKey = 'order_report_' . md5(json_encode([$option, $dates]));
        $cachedData = RedisCacheHelper::get($cacheKey);
        
        if ($cachedData) {
            return $cachedData;
        }

        // $res = [
        //     'count' => 0,
        //     'total_price_with_discount' => 0,
        //     'total_price_without_discount' => 0,
        //     'quantity' => 0,
        //     'total_order_payment_price' => 0,
        //     'total_order_payment_quantity' => 0
        // ];

        list($currentMonth, $previousMonth) = $dates;

        $result = new stdClass;

        //Calculation for current month 
        $orderQCurrentMonth = Order::query()
            ->select(
                'id',
                'sold_quantity',
                'purchase_id',
                'single_sold_price',
                'discount_single_sold_price',
                'total_sold_price',
                'original_sold_price',
                'discount_percent',
                'package_extension_date',
                'date_of_sale',
                'tracking_number',
                'status',
                'is_paid',
            )
            ->with(['orderPayments.invoice:id,order_payment_id,invoice_number,invoice_date', 'package','purchase:id,name'])
            ->where('date_of_sale', '>=', $currentMonth[0])
            ->where('date_of_sale', '<=', $currentMonth[1])
            ->where('status', $option);

        $orders = $orderQCurrentMonth->get();

        // $totalOrderPaymentAmount = $orderQCurrentMonth->get()->sum(function ($order) {
        //     return $order->orderPayments->sum('price');
        // });

        // $totalOrderPaymentQuantity = $orderQCurrentMonth->get()->sum(function ($order) {
        //     return  $order->orderPayments->sum('quantity');
        // });

        $result->data = $orders->toArray();

        // $res['count'] = $orderQCurrentMonth->count();
        // $res['total_price_with_discount'] = $orderQCurrentMonth->sum('total_sold_price');
        // $res['original_price_without_discount'] = $orderQCurrentMonth->sum('original_sold_price');
        // $res['quantity'] = $orderQCurrentMonth->sum('sold_quantity');
        // $res['total_order_payment_price'] = number_format($totalOrderPaymentAmount, 2, '.', ',');
        // $res['total_order_payment_quantity'] = $totalOrderPaymentQuantity;

        // $result->current_month = $res;
        // End

        // $previousMonthCalculations = call_user_func_array([__CLASS__, 'orderCalcForPrevMonth'], [$previousMonth, $option, $previousMonth]);

        // $result->previous_month = $previousMonthCalculations;

        $result->data = array_map(function ($order) {
            return [
                'id' => $order['id'],
                'product_name' => $order['purchase']['name'],
                'tracking_number' => $order['tracking_number'],
                'quantity' => $order['sold_quantity'],
                'single_sold_price' => $order['single_sold_price'],
                'expected_price' => $order['total_sold_price'],
                'discount' => $order['discount_percent'],
                'date_of_sale' => $order['date_of_sale'],
                'price_paid' => $order['order_payments']['price'],
                'payment_reference' => $order['order_payments']['payment_reference'],
                'date_of_payment' => $order['order_payments']['date_of_payment'],
                'invoice_number' => $order['order_payments']['invoice']['invoice_number'],
                'invoice_date' => $order['order_payments']['invoice']['invoice_date'],
            ];
        }, $result->data);

        $headings = [
            'Id',
            'Product',
            'Tracking number',
            'Quantity',
            'Single price',
            'Expected price',
            'Discount',
            'Date of sale',
            'Price paid',
            'Payment reference',
            'Date of payment',
            'Invoice number',
            'Invoice date'
        ];

        $result->headings  = $headings;

        $resArr = json_decode(json_encode($result), true);

        RedisCacheHelper::put($cacheKey, $resArr, 3600); // Cache for 1 hour

        return $resArr;
    }

    public function generatePurchaseReport($option,$dates){
        $cacheKey = 'purchase_report_' . md5(json_encode([$option, $dates]));
        $cachedData = RedisCacheHelper::get($cacheKey);

        if ($cachedData) {
            return $cachedData;
        }

        list($currentMonth, $previousMonth) = $dates;

        $result = new stdClass;

        $purchaseQ = Purchase::query()
        ->select(
            'id',
            'name',
            'supplier_id',
            'quantity',
            'price',
            'discount_price',
            'total_price',
            'original_price',
            'discount_percent',
            'initial_quantity',
            'code',
            'status',
            'expected_date_of_payment',
            'created_at'
        )
        ->with(['payment.invoice','supplier:id,name'])
        ->where('created_at', '>=', $currentMonth[0])
        ->where('created_at', '<=', $currentMonth[1])
        ->where('status', $option);

        $purchases = $purchaseQ->get();
        
        $result->data = $purchases->toArray();

        $result->data = array_map(function($purchase){
            return [
                "id" => $purchase['id'],
                "code" => $purchase['code'],
                'supplier' => $purchase['supplier']['name'],
                "name" => $purchase['name'],
                'quantity' => $purchase['quantity'],
                'initial_quantity' => $purchase['initial_quantity'],
                'price' => $purchase['price'],
                'discount_price' => $purchase['discount_price'],
                'discount' => $purchase['discount_percent'],
                'expected_price' => $purchase['total_price'],
                'expected_date_of_payment' => $purchase['expected_date_of_payment'],
            ];
        },$result->data);

        $headings = [
            'Id',
            'Code',
            'Supplier',
            'Name',
            'Quantity',
            'Initial quantity',
            'Single price',
            'Discount price',
            'Discount',
            'Price Paid',            
            'Expected date of payment',
        ];

        $result->headings  = $headings;

        $resArr = json_decode(json_encode($result), true);

        RedisCacheHelper::put($cacheKey, $resArr, 3600); // Cache for 1 hour

        return $resArr;

    }

    public function generateDriverReport($option,$dates,$dataSubExport = null){
        dd($dataSubExport);
    }

    public function generateReports($option,$data_export,$month,$dataSubExport = null)
    {
        $reportMethods = [
            '1' => 'generateOrderReport',
            '2' => 'generatePurchaseReport',
            '3' => 'generateDriverReport',
            '4' => 'generatePackageReport',
            '5' => 'generateCustomerReport',
        ];
        
        $methodName = $reportMethods[$data_export];

        $result = $this->invokeReportGeneration(
            $methodName,
            $option,
            $month,
            $dataSubExport
        );

        return $result;

    }

    public function invokeReportGeneration($methodName, $option, $month, $dataSubExport = null)
    {
        if (method_exists($this, $methodName)) {
            return $this->$methodName($option, $this->dates($month),$dataSubExport);
        }
        return null; // Handle method not found
    }

    public function dates($date)
    {

        $date = $this->helper->dateRangeConverter($date);
        $startDate = strtotime(date('Y-m-d 00:00:00', $date[0]));
        $endDate = strtotime(date('Y-m-d 23:59:59', $date[1]));

        $previousMonthStartDate = strtotime('-1 month', strtotime(date('Y-m-01 00:00:00', $startDate)));
        $previousMonthEndDate = strtotime('-1 second', strtotime(date('Y-m-t 23:59:59', $previousMonthStartDate)));

        $dates = [
            [
                date('Y-m-d H:i:s', $startDate),
                date('Y-m-d H:i:s', $endDate),
            ],
            [
                date('Y-m-d H:i:s', $previousMonthStartDate),
                date('Y-m-d H:i:s', $previousMonthEndDate),
            ],
        ];
        return $dates;
    }
}
