<?php

namespace App\Services;

use DateTime;
use App\Models\Product;

class SupplierSummaryService
{
    protected $date;
    protected $supplier;

    public function __construct($supplier, $date)
    {
        $this->supplier = $supplier;
        $this->date = $date;
    }

    public function productQueryBuilder() {
        $supplier = Product::select('id','name','supplier_id','status','created_at','quantity','price','total_price')
        ->where('supplier_id',$this->supplier)
        ->where('status','enabled')
        ->where('quantity','>',0);

        if (!empty($this->date)) {

            $dates = explode(" - ", $this->date);
            $date1 = new DateTime($dates[0]);
            $date2 = new DateTime($dates[1]);
            $date1_formatted = $date1->format('Y-m-d');
            $date2_formatted = $date2->format('Y-m-d');

            $supplier->whereBetween('created_at', [
                $date1_formatted,
                $date2_formatted
            ]);
        }

        return $supplier;
    }
    
    public function getProductsCount()
    {
        return $this->productQueryBuilder()->count();
    }

    public function getTotalSales()
    {
        return $this->productQueryBuilder()->sum('total_price');
    }

    public function getSummary(){
        $summary = new \stdClass;
        $summary->products_count = $this->getProductsCount();
        $summary->date = $this->date;
        $summary->total_sales = number_format($this->getTotalSales(), 2, '.', '');
        $summary->products = $this->productQueryBuilder()->get();
        return $summary;
    }

}
