<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaobaoTrade extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['tid'])) {
            $query->where('tid', $filters['tid']);
        }

        if (isset($filters['buyerNick']) && !empty($filters['buyerNick'])) {
            $query->where('buyer_nick', $filters['buyerNick']);
        }

        if (isset($filters['startDate']) &&  !empty($filters['startDate'])) {
            $query->where('created', '>=', $filters['startDate']);
        }
        if (isset($filters['status'])  && $filters['status'] != 99) {
            $query->where('handle_status', $filters['status']);
        }
        if (isset($filters['endDate']) && !empty($filters['endDate'])) {
            $query->where('created', '<=', $filters['endDate']." 23:59:59");
        }
        if (isset($filters['gameId'])) {
            $query->where('game_id', $filters['gameId']);
        }
        if (isset($filters['type']) && isset($filters['gameId'])) {
            $goodsId = AutomaticallyGrabGoods::where('type', $filters['type'])
                ->where('game_id', $filters['gameId'])
                ->value('foreign_goods_id');

            $query->where('num_iid', $goodsId)->where('game_id', $filters['gameId']);
        }
    }

    /**
     * 关联内部订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'tid', 'foreign_order_no');
    }

    /**
     * 获取交易状态对应的文字
     * @return mixed
     */
    public function getTradeStatusText()
    {
        return config('order.taobao_trade_status')[$this->trade_status];
    }

    /**
     * 如果有对应的订单则获取平台订单状态对应的文字
     * @return mixed
     */
    public function getOrderStatusText()
    {
        return isset($this->order->status) ? config('order.status_leveling')[$this->order->status] : '';
    }

    /**
     * 宝贝订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilterBaby($query, $filters = [])
    {
        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {
            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {
            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }
        return $query;
    }

}
