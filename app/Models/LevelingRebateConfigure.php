<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingRebateConfigure extends Model
{
    protected $fillable = ['game_id', 'game_name', 'game_leveling_type', 'level_count', 'rebate'];
}
