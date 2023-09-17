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

    protected $fillable = [
        'id',
        'order_id',
        'alias',
        'price',
        'quantity',
        'date_of_payment',
        'payment_method',
        'payment_reference',
        'payment_status'
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
