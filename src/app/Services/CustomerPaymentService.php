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
        $customer_payments = CustomerPayment::query()

        ->with(['order' => function ($query) {
            $query
                  ->select([
                    'id',
                    'customer_id',
                    'product_id',
                    'sold_quantity',
                    'single_sold_price',
                    'total_sold_price',
                    'discount_percent',
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

        $formated_dates = $this->dateFormat('query_format');

        if($formated_dates) {
            $customer_payments->whereBetween('date_of_payment', [
                $formated_dates['start'],
                $formated_dates['end']
            ]);
        }

        return $customer_payments;
    }

    public function dateFormat($option = null){
        
        if(!empty($this->date)) {
            $dates = explode(" - ", $this->date);
            $date1 = new DateTime($dates[0]);
            $date2 = new DateTime($dates[1]);
            $date1_formatted = $date1->format('Y-m-d');
            $date2_formatted = $date2->format('Y-m-d');
        }
        
        if($option === 'string_format') {
            return isset($date1_formatted) && isset($date1_formatted) ? 
            date('F j, Y', strtotime($date1_formatted)) . ' - ' . date('F j, Y', strtotime($date2_formatted)) 
            : '';
        } 
        elseif($option === 'query_format') {
            return isset($date1_formatted) && isset($date1_formatted) ? [
                'start' => $date1_formatted,
                'end' => $date2_formatted
            ] : false;
        }

    }

    public function getData(){
        $result = new stdClass();

        $formated_dates = $this->dateFormat('string_format');
        
        $result->date_range = $formated_dates;
        $result->customer = $this->getCustomer();
        $result->products = $this->orderQueryBuilder()->get()->toArray();
        $result->sum = $this->orderQueryBuilder()->sum('price');

        $totalDiscount = 0;
        $regularPrice = 0;

        foreach ($result->products as &$product) {
            $totalDiscount += $product['order']['discount_percent'];
            
            $dateOfSale = strtotime($product['order']['date_of_sale']);
            $dateOfPayment = strtotime($product['date_of_payment']);
            $daysDelayed = floor(($dateOfPayment - $dateOfSale) / (60 * 60 * 24));
            
            $product['order']['delayed_payment'] = $daysDelayed;
        }

        $regularPrice = ($result->sum / (1-($totalDiscount/100)));

        $result->total_discount = $totalDiscount;
        $result->regular_price = number_format($regularPrice, 2, '.', '');

        return $result;
    }
}
