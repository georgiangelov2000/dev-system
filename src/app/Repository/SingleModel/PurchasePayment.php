<?php

namespace App\Repository\SingleModel;

class PurchasePayment implements ModelRepository
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {

    }

    public static function data($model) {
        $model->expected_date_of_payment = now()->parse($model->expected_date_of_payment)->format('F j, Y');
        $model->expected_delivery_date = now()->parse($model->purchase->expected_delivery_date)->format('F j, Y');
        $model->date_of_payment_formatting = now()->parse($model->date_of_payment)->format('F j, Y');

        if($model->invoice->invoice_date) {now()->parse($model->expected_date_of_payment)->format('F j, Y');
            $model->invoice_date = now()->parse($model->invoice->invoice_date)->format('F j, Y');
        }
        
        return $model;
    }
}
