<?php 
return [
    "order_statuses" => [
        1 => "Received",
        3 => "Pending",
        4 => "Ordered"
    ],
    "package_types" => [
        1 => "Standart",
        2 => "Express",
        3 => "Overnight"
    ],
    'delivery_methods' => [
        1 => "Ground",
        2 => "Air",
        3 => "Sea"
    ],
    'is_paid_statuses' => [
        0 => false,
        1 => true
    ],
    'payment_methods_statuses' => [
        0 => '',
        1 => 'Cash',
        2 => 'Bank Transfer',
        3 => 'Credit Card',
        4 => 'Cheque',
        5 => 'Online Payment'
    ],
    'payment_statuses' => [
        0 => '',
        1 => 'Pending',
        2 => 'Paid',
        3 => 'Partially Paid',
        4 => 'Overdue',
        5 => 'Refunded',
    ]
]

?>