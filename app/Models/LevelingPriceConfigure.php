<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingPriceConfigure extends Model
{
    protected $fillable = ['game_id', 'game_name', 'game_leveling_type', 'game_leveling_number',
		'game_leveling_level', 'level_price', 'level_hour', 'level_security_deposit',
		'level_efficiency_deposit',
	];

    public static function scopeFilter($query, $filters = [])
    {
    	if (isset($filters['gameLevelingNumber']) && ! empty($filters['gameLevelingNumber'])) {
    		$query->where('game_leveling_number', $filters['gameLevelingNumber']);
    	}
    	return $query;
    }

}
