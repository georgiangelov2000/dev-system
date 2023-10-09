<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use App\Models\Customer;
use App\Models\Supplier;

class Country extends Model
{
    
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

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

     public function states(){
        return $this->hasMany(State::class,'country_id');
     }

     public function customers()
     {
         return $this->hasManyThrough(Customer::class, State::class);
     }
 
     public function suppliers()
     {
         return $this->hasManyThrough(Supplier::class, State::class);
     }
}
