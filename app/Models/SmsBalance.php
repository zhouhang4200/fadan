<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsBalance extends Model
{
    public $fillable = [
        'user_id',
        'amount'
    ];
}
