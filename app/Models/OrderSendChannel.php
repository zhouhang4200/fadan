<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSendChannel extends Model
{
    protected $fillable = ['user_id', 'third', 'game_id', 'game_name', 'created_at', 'updated_at'];

}
