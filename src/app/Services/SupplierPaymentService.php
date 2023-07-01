<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use stdClass;

class SupplierPaymentService
{
    public $supplier;

    public $date;

    public function __construct($supplier, $date )
    {
        $this->supplier = $supplier;
        $this->date = $date;
    }
    
    public function supplierQuery(){
        return Supplier::select(
            'id',
            'name',
            'email',
            'phone',
            'address',
            'zip',
            'website',
            'state_id',
            'country_id'
        )->with(['state:id,name','country:id,name,short_name'])
        ->find($this->supplier);
    }

    public function customerPaymentQuery(){
        $paymentQ = SupplierPayment::query()
        ->with(['purchase' => function($query){
            $query->select(
                'id',
                'name',
                'supplier_id',
                'quantity',
                'price',
                'total_price',
                'initial_quantity',
                'notes',
                'code',
                'status',
                'is_paid'
            )
            ->where('is_paid', 1);
        }])
        ->whereHas('purchase', function ($query) {
            $query->where('supplier_id', $this->supplier);
        });
        
        $formated_dates = $this->dateFormat();

        if($formated_dates) {
            $paymentQ
            ->where('date_of_payment','>=',$formated_dates[0])
            ->where('date_of_payment','<=',$formated_dates[1]);
        };

        return $paymentQ;
    }

    public function dateFormat()
    {
        if (!empty($this->date)) {
            $dates = explode(" - ", $this->date);
            $date1 = $dates[0];
            $date2 = $dates[1];
    
            $date1_formatted = date('Y-m-d', strtotime($date1));
            $date2_formatted = date('Y-m-d', strtotime($date2));
    
            if (strtotime($date1) !== false && strtotime($date2) !== false) {
                return [
                    $date1_formatted,
                    $date2_formatted
                ];
            }
        }
    
        return null;
    }

    public function getPayment(){
        $data = $this->customerPaymentQuery()->get();

        foreach ($data as $key => $value) {
            
        }
    }

    public function getData(){
        $result = new stdClass();
        $formated_dates = $this->dateFormat();
        $dateToString = $formated_dates ? date('F j, Y', strtotime($formated_dates[0])) . ' - ' . date('F j, Y', strtotime($formated_dates[1])) : '';

        $result->date_range = $dateToString;
        $result->supplier = $this->supplierQuery();
        $result->payments = $this->customerPaymentQuery()->get();

        $result->sum = number_format($this->customerPaymentQuery()->sum('price'),2,'.','');

        return $result;
    }

}
