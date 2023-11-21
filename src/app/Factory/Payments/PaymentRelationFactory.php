<?php

namespace App\Factory\Payments;

use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Repository\SingleModel\OrderPayment as OrderPaymentRepository;
use App\Repository\SingleModel\PurchasePayment as PurchasePaymentRepository;

class PaymentRelationFactory
{

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

    public static function selectBuilder($builder)
    {

        if ($builder instanceof OrderPayment) {
            return OrderPaymentRepository::data($builder);
        }
        if ($builder instanceof PurchasePayment) {
            return PurchasePaymentRepository::data($builder);
        }
    }

}
