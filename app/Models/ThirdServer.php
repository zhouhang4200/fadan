<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdServer extends Model
{
    protected $fillable = [
    	'game_id', 'server_id', 'third_server_id', 'third_id', 'created_at', 'updated_at', 
    	'server_name', 'third_server_name',
    ];
}
