<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfterService extends Model
{
    public $timestamps = false;

    public $fillable = [
        'order_no',
        'order_creator_user_id',
        'order_gainer_user_id',
        'apply_admin_user_id',
        'original_amount',
        'refund_amount',
        'apply_remark',
        'apply_date',
        'auditing_admin_user_id',
        'auditing_date',
        'auditing_remark',
        'confirm_date',
        'confirm_admin_user_id',
    ];

    /**
     * 条件过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['orderNo']) && $filters['orderNo']) {
            $query->where('order_no', $filters['orderNo']);
        } else {
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['orderCreatorUserId'])) {
                $query->where('order_creator_user_id', $filters['orderCreatorUserId']);
            }
        }
    }

    /**
     * 申请人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function applyUser()
    {
        return $this->hasOne(AdminUser::class, 'id', 'apply_admin_user_id');
    }

    /**
     * 审核人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function auditingUser()
    {
        return $this->hasOne(AdminUser::class, 'id', 'auditing_admin_user_id');
    }

    /**
     * 确认人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function confirmUser()
    {
        return $this->hasOne(AdminUser::class, 'id', 'confirm_admin_user_id');
    }
}
