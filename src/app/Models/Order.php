<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerPayment;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "orders";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        "customer_id",
        "product_id",
        "date_of_sale",
        "status",
        "product_id",
        "invoice_number",
        "sold_quantity",
        "single_sold_price",
        "total_sold_price",
        "discount_percent",
        "package_id",
        "tracking_number"
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function package(){
        return $this->belongsTo(Package::class);
    }

    public function customerPayments(){
        return $this->hasMany(CustomerPayment::class);
    }
}
