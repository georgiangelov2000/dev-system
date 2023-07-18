<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchasePayment;
class InvoicePurchase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "invoice_purchases";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'purchase_payment_id',
        'invoice_number',
        'invoice_date',
        'price',
        'quantity',
        'status'
    ];

    public function purchasePayment(){
        return $this->belongsTo(PurchasePayment::class);
    }
}
