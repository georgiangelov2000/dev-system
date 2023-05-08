<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayments extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "customer_payments";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'order_id',
        'customer_id',
        'price',
        'date_of_payment'
    ];

}
