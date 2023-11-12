<?php

namespace App\Services;

use App\Adapters\Views\PaymentView;
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
        $this->helper = $helper;

        // Initialize indexMapping with customer and supplier data
        $this->indexMapping = [
            'order' => [
                'customers' => Customer::select('id', 'name')->get()
            ],
            'purchase' => [
                'suppliers' => Supplier::select('id', 'name')->get()
            ]
        ];

        // Initialize editMapping with query builders for order and purchase payments
        $this->editMapping = [
            'order' => OrderPayment::query(),
            'purchase' => PurchasePayment::query(),
        ];

        $this->paymentView = $paymentView;
    }

    /**
     * Retrieves and returns the view based on the payment type and payment ID (if provided).
     *
     * @param string $type - Type of payment ('order' or 'purchase').
     * @param string|null $payment - Payment ID (optional).
     * @return mixed - Returns a view or null if the type is not in the list.
     */
    public function getData(string $type, string $payment = null)
    {
        if (array_key_exists($type, $this->indexMapping) && !$payment) {
            $data = $this->indexMapping[$type];
        } elseif (array_key_exists($type, $this->editMapping) && $payment) {
            $relations = [];
            $builder = $this->editMapping[$type]->where('id', $payment)->first();

            if ($builder instanceof OrderPayment) {
                array_push($relations, 'order.customer', 'invoice');
            } elseif ($builder instanceof PurchasePayment) {
                array_push($relations, 'purchase.supplier', 'invoice');
            }

            $builder->load($relations);
            $data['payment'] = $builder;
            $data['settings'] = $this->helper->settings();

        }
        
        return view($this->paymentView->getView($type,$payment),$data) ;
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
