<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Package;
use App\Models\OrderPayment;

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

    const IS_IT_DELIVERED_TRUE = 1;
    const IS_IT_DELIVERED_FALSE = 0;

    const PAID = 1;
    const PENDING = 2;
    const OVERDUE = 4;

    protected $fillable = [
        "id",
        "customer_id",
        "expected_delivery_date",
        'delivery_date',
        'user_id',
        "status",
        "purchase_id",
        "sold_quantity",
        "single_sold_price",
        'discount_single_sold_price',
        "total_sold_price",
        "original_sold_price",
        "discount_percent",
        "package_id",
        "tracking_number",
        'package_extension_date',
        'is_it_delivered'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function package()
    {   
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }
}
