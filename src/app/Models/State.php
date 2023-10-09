<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Supplier;

class State extends Model {

    protected $fillable = ['country_id','name'];

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';

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

     public function country()
     {
         return $this->belongsTo(Country::class, 'country_id', 'id');
     }

     public function customer()
     {
         return $this->hasMany(Customer::class);
     }

     public function supplier()
     {
         return $this->hasMany(Supplier::class);
     }
}
