<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "packages";

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
        "tracking_number",
        "customer_id",
        "package_name",
        "package_type",
        "delievery_method",
        "delievery_date",
        "package_notes",
        "customer_notes",
        "order_id",
    ];

}
