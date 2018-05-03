<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HatchetManBlacklist extends Model
{
    protected $fillable = ['user_id', 'hatchet_man_name', 'hatchet_man_qq', 'hatchet_man_phone', 'content'];

    public static function scopeFilter($query, $filters = [])
    {
    	if (isset($filters['hatchetManName'])) {
    		$query->where('hatchet_man_name', $filters['hatchetManName']);
    	}

    	if (isset($filters['hatchetManPhone'])) {
    		$query->where('hatchet_man_phone', $filters['hatchetManPhone']);
    	}

    	if (isset($filters['hatchetManQq'])) {
    		$query->where('hatchet_man_qq', $filters['hatchetManQq']);
    	}

    	return $query;
    }
}
