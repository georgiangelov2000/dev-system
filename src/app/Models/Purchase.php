<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\PurchaseImage;
use App\Models\Supplier;

class Purchase extends Model {
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchases';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'name',
        'quantity',
        'price',
        'supplier_id',
        'category_id',
        'subcategories',
        'code',
        'notes',
        'brands',
        'total_price',
        'initial_quantity',
        'is_paid',
        'status'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'purchases_categories');
    }

    public function subcategories() {
        return $this->belongsToMany(SubCategory::class, 'purchases_subcategories');
    }

    public function brands() {
        return $this->belongsToMany(Brand::class, 'purchases_brands');
    }

    public function images() {
        return $this->hasMany(PurchaseImage::class, 'purchase_id');
    }
    
    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function orders(){
        return $this->hasMany(Order::class,'purchase_id');
    }

    public function payments(){
        return $this->hasMany(PurchasePayment::class,'purchase_id');
    }

}