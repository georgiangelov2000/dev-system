<?php 
return [
    "order_statuses" => [
        1 => 'Paid',
        2 => 'Pending',
        3 => 'Partially Paid',
        4 => 'Overdue',
        5 => 'Refunded',
        6 => 'Ordered',
    ],
    'payment_statuses' => [
        1 => 'Paid',
        2 => 'Pending',
        3 => 'Partially Paid',
        4 => 'Overdue',
        5 => 'Refunded',
    ],
    "purchase_statuses" => [
        1 => 'Paid',
        2 => 'Pending',
        3 => 'Partially Paid',
        4 => 'Overdue',
        5 => 'Refunded',
        6 => 'Delivered',
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
        1 => true,
    ],
    'payment_methods_statuses' => [
        1 => 'Cash',
        2 => 'Bank Transfer',
        3 => 'Credit Card',
        4 => 'Cheque',
        5 => 'Online Payment'
    ],
    'stock_statuses' => [
        0 => 'In stock',
        1 => 'Out of stock',
    ],
    'genders' => [
        1 => 'Male',
        2 => 'Female',
        3 => 'Other'
    ],
    'roles' => [
        1 => 'Admin',
        2 => 'Driver'
    ],
    'settings_type' => [
        1 => 'Company Information',
    ],
]

?>