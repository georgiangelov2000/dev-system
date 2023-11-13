<?php

namespace App\Services;

use App\Factory\Views\PaymentView;
use App\Helpers\FunctionsHelper;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;

class PaymentService
{
    private $indexMapping = [];

    private $editMapping = [];

    private $helper;

    private $paymentView;

    public function __construct(FunctionsHelper $helper, PaymentView $paymentView)
    {
        // Initialize editMapping with query builders for order and purchase payments
        $this->editMapping = [
            'order' => OrderPayment::query(),
            'purchase' => PurchasePayment::query(),
        ];

        $this->paymentView = $paymentView;
    }



    /**
     * Retrieves and returns an instance of a payment model by ID and type.
     *
     * @param int $payment - Payment ID.
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return mixed - Returns an instance of the payment model.
     */
    public function getInstance($payment, $type)
    {
        $builder = $this->editMapping[$type]->find($payment);
        return $builder;
    }
}
