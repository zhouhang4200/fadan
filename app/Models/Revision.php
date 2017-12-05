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

        return $query->latest('created_at')->where('user_table', 'users');
    }

    // 多对已
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 多对一
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'user_id', 'id');
    }

     /**
     * 查找 显示 登录详情
     * @var [type]
     */
    public static function scopePunishFilter($query, $filters = [])
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

        if ($filters['orderId']) {

            $punishIds = PunishOrReward::where('order_id', $filters['orderId'])->pluck('id');

            $query->whereIn('revisionable_id', $punishIds);
        }

        return $query->latest('created_at');
    }

    public function punishOrReward()
    {
        return $this->belongsTo(PunishOrReward::class, 'revisionable_id', 'id');
    }
}
