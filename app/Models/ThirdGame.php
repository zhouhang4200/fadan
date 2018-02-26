<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdGame extends Model
{
    protected $fillable = [
    	'third_id', 'game_id', 'third_game_id', 'crated_at', 'updated_at', 'game_name', 'third_game_name',
    ];
}
