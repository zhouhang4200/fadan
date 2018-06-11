<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessmanComplaint extends Model
{
    public  $statusText = [
      1 => '投诉',
      2 => '已取消',
      3 => '投诉成功',
      4 => '投诉失败',
    ];

    public $fillable = [
      'complaint_primary_user_id',
      'be_complaint_primary_user_id',
      'order_no',
      'amount',
      'remark',
    ];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['orderNo']) && $filters['orderNo']) {
            $no = OrderDetail::where('field_value', $filters['orderNo'])
                ->whereIn('field_name', ['third_order_no', 'source_order_no'])
                ->value('order_no');
            $query->where('order_no', $no);
        }
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['gameId']) && $filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }
        if (isset($filters['startDate']) &&  $filters['startDate']) {
            $query->where('created_at', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && $filters['endDate']) {
            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }
    }

    /**
     * 获取状态
     * @return string
     */
    public function statusText()
    {
        return isset($this->statusText[$this->status]) ? $this->statusText[$this->status] : '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'order_no');
    }

    /**
     * 订单外部订单关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taobaoTrade()
    {
        return $this->belongsTo(TaobaoTrade::class, 'foreign_order_no', 'tid');
    }
}
