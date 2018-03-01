<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdArea extends Model
{
    protected $fillable = [
    	'game_id', 'area_id', 'third_area_id', 'created_at', 'updated_at', 'third_id', 'area_name', 'third_area_name',
    ];
}
