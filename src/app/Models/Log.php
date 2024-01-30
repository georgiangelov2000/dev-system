<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'id',
        'action',
        'user_id',
        'message',
        'created_at',
        'updated_at'
    ];
}
