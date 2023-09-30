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
        'payment_status',
        'partially_paid_price'
    ];

    private $statuses; // Declare statuses as a class property
    private $methods; // Declare methods as a class property

    public function __construct()
    {
        parent::__construct();
        $this->statuses = config('statuses.payment_statuses'); // Initialize statuses in the constructor
        $this->methods = config('statuses.payment_methods_statuses'); // Initialize methods in the constructor
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function invoice()
    {
        return $this->hasOne(InvoiceOrder::class, 'order_payment_id');
    }

    public function statusValidation(string $status = null): ?string
    {
        return array_key_exists($status, $this->statuses) ? $status : (array_key_exists($this->payment_status, $this->statuses) ? $this->payment_status : null);
    }
    
    public function methodValidation(string $method = null): ?string
    {
        return array_key_exists($method, $this->methods) ? $method : (array_key_exists($this->payment_method, $this->methods) ? $this->payment_method : null);
    }
    
}
