<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "settings";

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
        'settings_description',
        'type',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public static function getStruct(){
        return [
            'email' => null,
            'name' => null,
            'country' => null,
            'state' =>  null,
            'phone_number' => null,
            'tax_number' => null,
            'address' => null,
            'website' => null,
            'owner_name' => null,
            'business_type' => null,
            'registration_date' => date('Y-m-d'),
            'image_path' => null,
            'notification_email' => null
        ];
    }
}
