<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformAmountFlow extends Model
{
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    public function tradeOrder()
    {
        return $this->morphTo();
    }
}
