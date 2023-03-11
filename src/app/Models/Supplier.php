<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use App\Models\SupplierImage;
use App\Models\Category;

class Supplier extends Model {

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suppliers';

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
        'id',
        'name',
        'email',
        'phone',
        'address',
        'zip',
        'website',
        'notes',
        'state_id',
        'country_id'
    ];

    public function states() {
        return $this->belongsTo(State::class, 'state_id');
    }
    
    public function image(){
        return $this->hasOne(SupplierImage::class,'supplier_id');
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    
    public function categories(){
        return $this->belongsToMany(Category::class,'suppliers_categories');
    }
}
