<?php

namespace App\Services;

use DateTime;
use App\Models\Order;
use App\Models\Customer;

class CustomerPaymentService
{
    public $customer;
    public $date;

    public function __construct($customer, $date)
    {
        $this->customer = $customer;
        $this->date = $date;
    }
}
