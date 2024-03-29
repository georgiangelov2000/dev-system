<?php

namespace App\Repository\SingleModel;

class OrderPayment implements ModelRepository
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {

    }

    public static function data($model)
    {
        $model->load(['order.purchase:id,image_path']);
        $model->expected_date_of_payment = now()->parse($model->expected_date_of_payment)->format('F j, Y');
        $model->expected_delivery_date = now()->parse($model->order->expected_delivery_date)->format('F j, Y');
        $model->date_of_payment_formatting = now()->parse($model->date_of_payment)->format('F j, Y');

        if($model->invoice->invoice_date) {
            $model->invoice->invoice_date = now()->parse($model->invoice->invoice_date)->format('F j, Y');
        }
        
        return $model;
    }
}
