<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

/**
 * Class Order
 * @package App\Models
 */
class SteamOrder extends Model
{

    //黑名单为空
    protected $guarded = [];
    /**
     * 订单详情
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'no');
    }

    /**
     * 订单操作历史记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_no', 'no');
    }

    /**
     * 订单资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    /**
     * 订单平台资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    public function goodses()
    {
        return $this->hasOne(SteamGoods::class,'id', 'goods_id');
    }

}
