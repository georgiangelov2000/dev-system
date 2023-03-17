<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductImage;
use App\Models\Supplier;

class Product extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

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
        'total_price'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function subcategories() {
        return $this->belongsToMany(SubCategory::class, 'product_subcategories');
    }

    public function brands() {
        return $this->belongsToMany(Brand::class, 'product_brands');
    }

    public function images() {
        return $this->hasOne(ProductImage::class, 'product_id');
    }
    
    public function suppliers() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

}
