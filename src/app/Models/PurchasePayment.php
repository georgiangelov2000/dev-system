<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
use App\Models\InvoicePurchase;

class PurchasePayment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "purchase_payments";

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
    const REFUNDED = 5;

    protected $fillable = [
        'purchase_id',
        'alias',
        'quantity',
        'price',
        'date_of_payment',
        'payment_method',
        'payment_reference',
        'expected_date_of_payment',
        'payment_status',
        'delivery_status'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function invoice()
    {
        return $this->hasOne(InvoicePurchase::class, 'purchase_payment_id');
    }
}
