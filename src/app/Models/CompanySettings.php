<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\State;

class CompanySettings extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "company_settings";

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
        "name",
        "email",
        "country_id",
        "state_id",
        "phone_number",
        "tax_number",
        "address",
        "website",
        "owner_name",
        "bussines_type",
        "image_path"
    ];
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function state() {
        return $this->belongsTo(State::class, 'state_id');
    }

}
