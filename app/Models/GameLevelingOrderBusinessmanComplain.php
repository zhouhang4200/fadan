<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GameLevelingOrderBusinessmanComplain
 * @package App\Models
 */
class GameLevelingOrderBusinessmanComplain extends Model
{
    public $fillable = [
        'from_user_id',
        'to_user_id',
        'game_leveling_order_trade_no',
        'amount',
        'reason',
        'result',
        'status',
        'images',
    ];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['order_no']) && $filters['order_no']) {
            $query->whereHas('gameLevelingOrder', function ($query) use ($filters) {
                return $query->where('trade_no', $filters['order_no'])
                    ->orWhere('platform_trade_no', $filters['order_no']);
            });
        }

        if (isset($filters['status']) && $filters['status'] != 99) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['game_id']) && $filters['game_id']) {
            $query->whereHas('gameLevelingOrder', function ($query) use ($filters) {
                return $query->where('game_id', $filters['game_id']);
            });
        }

        if (isset($filters['created_at']) &&  $filters['created_at']) {
            $query->whereBetween('created_at', $filters['created_at']);
        }

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder()
    {
        return $this->belongsTo(GameLevelingOrder::class, 'game_leveling_order_trade_no', 'trade_no');
    }


    /**
     * 被投诉人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    /**
     * 投诉人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }
}
