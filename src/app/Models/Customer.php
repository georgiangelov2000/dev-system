<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use App\Models\Country;
use App\Models\Order;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

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

    public function state() {
        return $this->belongsTo(State::class, 'state_id');
    }
    
    public function image(){
        return $this->hasOne(CustomerImage::class,'customer_id');
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
