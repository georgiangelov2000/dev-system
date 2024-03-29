<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\Purchase;

class Category extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','name','description','image_path'];
    
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class,'suppliers_categories');
    }

    public function products()
    {
        return $this->belongsToMany(Purchase::class,'purchases_categories');
    }
}
