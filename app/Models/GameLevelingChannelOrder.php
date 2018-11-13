<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelOrder extends Model
{
    public $fillable = [
        'trade_no',
        'user_id',
        'game_leveling_channel_user_id',
        'amount',
        'discount_amount',
        'payment_amount',
        'refund_amount',
        'payment_type',
        'status',
        'game_id',
        'game_name',
        'game_region_id',
        'game_region_name',
        'game_server_id',
        'game_server_name',
        'game_leveling_type_id',
        'game_leveling_type_name',
        'game_role',
        'game_account',
        'game_password',
        'player_phone',
        'player_qq',
        'user_qq',
        'title',
        'day',
        'hour',
        'demand',
        'security_deposit',
        'efficiency_deposit',
        'explain',
        'requirement',
        'remark',
    ];

    /**
     * 渠道C端用户表
     */
    public function gameLevelingChannelUser()
    {
        $this->belongsTo(GameLevelingChannelUser::class);
    }

    public function gameLevelingOrders()
    {
        return $this->hasMany(GameLevelingOrder::class, 'channel_order_trade_no', 'trade_no');
    }

    /**
     * 渠道列表筛选
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public function scopeFilter($query, $filter = [])
    {
        if ($filter['tradeNo']) {
            $query->where('trade_no', $filter['tradeNo']);
        }

        if ($filter['gameName']) {
            $query->where('game_name', $filter['gameName']);
        }

        if (isset($filter['status']) && ! empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        if ($filter['startDate']) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if ($filter['endDate']) {
            $query->where('created_at', '<=', $filter['endDate']);
        }
        return $query;
    }

    public function gameLevelingChannelRefund()
    {
        return $this->hasOne(GameLevelingChannelRefund::class, 'game_leveling_channel_order_trade_no', 'trade_no');
    }
}
