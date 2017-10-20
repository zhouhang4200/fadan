<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    /**
     * 查找 显示 登录详情
     * @var [type]
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate'] . " 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate'] . " 23:59:59"]); 
        }

        return $query->latest('created_at');
    }

    /**
     * 查找 显示 登录详情
     * @var [type]
     */
    public static function scopeUserFilter($query, $filters = [])
    {
        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate'] . " 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate'] . " 23:59:59"]); 
        }

        return $query->latest('created_at')->where('user_Id', Auth::id())->where('user_table', 'users');
    }
}
