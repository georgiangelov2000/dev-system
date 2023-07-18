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

    protected $fillable = [
        'id',
        'purchase_id',
        'quantity',
        'price',
        'date_of_payment',
        'payment_method',
        'payment_reference',
        'payment_status',
        'notes'
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
