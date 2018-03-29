<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatistic extends Model
{
    protected $fillable = [
        'user_id', 'parent_id', 'name', 'user_name', 'complete_order_count', 
        'send_order_amount', 'revoke_order_count', 'arbitrate_order_count', 'profit', 'date'
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function children()
    {
    	return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
    	return $this->belongsTo(static::class, 'parent_id');
    }

    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['userName']) {
            $query->where('user_id', $filters['userName']);
        }

        if (isset($filters['startDate']) && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $query->whereBetween('date', [$filters['startDate'], $filters['endDate']]);
        }

        return $query;
    }
}
