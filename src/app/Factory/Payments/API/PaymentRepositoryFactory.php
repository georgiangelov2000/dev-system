<?php

namespace App\Factory\Payments\API;
use App\Repository\API\PurchasePaymentRepository;
use App\Repository\API\OrderPaymentRepository;

class PaymentRepositoryFactory
{

    private static $types = [
        'order' => OrderPaymentRepository::class,
        'purchase' => PurchasePaymentRepository::class,
    ];

    private function __construct()
    {

    }

    public static function create($type)
    {
        if (self::$types[$type]) {
            $className = self::$types[$type];

            if (class_exists($className)) {
                $instance = new $className;
            }

            return $instance;
        } else {
            throw new NotFoundHttpException();
        }
    }
}
