<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSendChannel extends Model
{
    protected $fillable = ['user_id', 'third', 'game_id', 'created_at', 'updated_at'];

}
