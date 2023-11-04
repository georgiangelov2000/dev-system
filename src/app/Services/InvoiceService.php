<?php

namespace App\Services;

use App\Models\InvoiceOrder;
use App\Models\InvoicePurchase;

class InvoiceService
{
    private $editMapping = [];

    public function __construct()
    {
        $this->editMapping = [
            'order' => InvoiceOrder::query(),
            'purchase' => InvoicePurchase::query()
        ];
    }

    public function getInstance($invoice,$type) {
        $builder = $this->editMapping[$type]->findOrFail($invoice);
        return $builder;
    }

}
