<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingConfigure extends Model
{
	protected $fillable = [
		'game_id', 'game_name', 'game_leveling_type', 'rebate', 'game_leveling_requirements',
		'game_leveling_instructions', 'user_qq', 'created_at', 'updated_at'
	];

	/**
	 * 筛选
	 * @param  [type] $query   [description]
	 * @param  [type] $filters [description]
	 * @return [type]          [description]
	 */
    public static function scopeFilter($query, $filters = [])
    {
    	if (isset($filters['gameId']) && ! empty($filters['gameId'])) {
    		$query->where('leveling_configures.game_id', $filters['gameId']);
    	}
    	return $query;
    }
}
