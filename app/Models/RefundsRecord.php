<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 失败订单退款记录
 * Class RefundsRecord
 * @package App\Models
 */
class RefundsRecord extends Model
{
    public $fillable = [
        'order_no',
        'amount',
        'auditor',
    ];
}
