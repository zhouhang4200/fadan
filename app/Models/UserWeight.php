<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWeight extends Model
{
    public $fillable = [
        'user_id',
        'weight',
        'weight_percent',
        'start_date',
        'end_date',
        'created_admin_user_id',
        'updated_admin_user_id',
    ];
}
