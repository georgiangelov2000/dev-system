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

    protected $fillable = [
        "customer_id",
        "date_of_sale",
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
        'package_extension_date'
    ];

    protected $statuses; // Declare statuses as a class property

    public function __construct()
    {
        parent::__construct();
        $this->statuses = config('statuses.order_statuses'); // Initialize statuses in the constructor
    }

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
