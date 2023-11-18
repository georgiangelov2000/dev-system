<?php 
return [
    'payment_statuses' => [
        1 => 'Paid',
        2 => 'Pending',
        3 => 'Partially Paid',
        4 => 'Overdue',
        5 => 'Refunded',
    ],
    'delivery_statuses' => [
        1 => 'Delivered',
        2 => 'Pending',
        4 => 'Overdue',
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
    'languages' => [
        'english',
        'portuguese',
        'german',
        'french',
        'spanish'
    ],
    'is_it_delivered' => [
        0 => 'Not delivered',
        1 => 'Delivered',
    ]
]

?>