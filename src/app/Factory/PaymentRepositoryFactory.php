<?php

namespace App\Factory;
use App\Repository\PurchasePaymentRepository;
use App\Repository\OrderPaymentRepository;

class PaymentRepositoryFactory
{

    private function __construct()
    {

    }

    public static function create($type)
    {
        if ($type === 'order') {
            return new OrderPaymentRepository();
        } elseif ($type === 'purchase') {
            return new PurchasePaymentRepository();
        } else {
            throw new \InvalidArgumentException('Invalid payment type');
        }
    }
}
