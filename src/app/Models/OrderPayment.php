<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceOrder;

class OrderPayment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "order_payments";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public $timestamps = false;

    // Payment status
    const SUCCESSFULLY_PAID_DELIVERED = 1;

    // FOR BOTH
    const PENDING = 2;
    const OVERDUE = 4;

    protected $fillable = [
        'order_id',
        'alias',
        'quantity',
        'price',
        'payment_method',
        'payment_reference',
        'date_of_payment',
        'expected_date_of_payment',
        'payment_status',
        'delivery_status'
        // 'partially_paid_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function invoice()
    {
        return $this->hasOne(InvoiceOrder::class, 'order_payment_id');
    }
}
