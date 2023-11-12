<?php

namespace App\Factory;
use App\Repository\PurchaseInvoiceRepository;
use App\Repository\OrderInvoiceRepository;

class InvoiceRepositoryFactory
{

    private function __construct()
    {

    }

    public static function create($type)
    {
        if ($type === 'order') {
            return new OrderInvoiceRepository();
        } elseif ($type === 'purchase') {
            return new PurchaseInvoiceRepository();
        } else {
            throw new \InvalidArgumentException('Invalid payment type');
        }
    }
}
    