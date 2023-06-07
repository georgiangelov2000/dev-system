<?php

namespace App\Services;

use App\Models\Customer;
use DateTime;
use App\Models\CustomerPayment;
use stdClass;

class CustomerPaymentService
{
    public $customer;
    public $date;

    public function __construct($customer, $date)
    {
        $this->customer = $customer;
        $this->date = $date;
    }

    public function getCustomer(){
    return Customer::select([
            'name',
            'email',
            'phone',
            'address',
            'zip',
            'website',
            'country_id',
            'state_id',
            'notes'
        ])->with(['state:id,name','country:id,name,short_name'])
        ->find($this->customer);
    }

    public function orderQueryBuilder()
    {
        $customer_payments = CustomerPayment::select('order_id','date_of_payment','price','quantity')
        ->with(['order' => function ($query) {
            $query
                  ->select([
                    'id',
                    'customer_id',
                    'product_id',
                    'discount_percent',
                    'single_sold_price',
                    'tracking_number',
                    'invoice_number',
                    'date_of_sale'
                  ])
                  ->where('is_paid', 1)
                  ->where('customer_id', $this->customer)
                  ->with('product:id,name');
    
        }])
        ->whereHas('order', function ($query) {
            $query->where('customer_id', $this->customer);
        });

        $formated_dates = $this->dateFormat();

        if($formated_dates) {
            $customer_payments->whereBetween('date_of_payment', [
                $formated_dates[0],
                $formated_dates[1]
            ]);
        }

        return $customer_payments;
    }

    public function dateFormat(){
        
        if(!empty($this->date)) {
            $dates = explode(" - ", $this->date);
            $date1_formatted = date('Y-m-d', strtotime($dates[0]));
            $date2_formatted = date('Y-m-d', strtotime($dates[1]));

            return [
                $date1_formatted,
                $date2_formatted
            ];
        }
    }

    public function getData(){
        $result = new stdClass();

        $formated_dates = $this->dateFormat();
        $dateToString = $formated_dates ? date('F j, Y', strtotime($formated_dates[0])) . ' - ' . date('F j, Y', strtotime($formated_dates[1])) : '';
        
        $result->date_range = $dateToString;
        $result->customer = $this->getCustomer();
        $result->products = $this->orderQueryBuilder()->get()->toArray();
        $result->sum = number_format($this->orderQueryBuilder()->sum('price'),2,'.','');

        $totalDiscount = 0;
        $regularPrice = 0;

        foreach ($result->products as &$product) {
            $totalDiscount += $product['order']['discount_percent'];
            
            $dateOfSale = strtotime($product['order']['date_of_sale']);
            $dateOfPayment = strtotime($product['date_of_payment']);
            $daysDelayed = floor(($dateOfPayment - $dateOfSale) / (60 * 60 * 24));
            
            $product['delayed_payment'] = $daysDelayed;
        }

        $regularPrice = ($result->sum / (1-($totalDiscount/100)));

        $result->total_discount = $totalDiscount;
        $result->regular_price = number_format($regularPrice, 2, '.', '');

        return $result;
    }
}
