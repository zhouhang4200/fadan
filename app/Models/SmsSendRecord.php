<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 短信发送记录
 * Class SmsSendRecord
 * @package App\Models
 */
class SmsSendRecord extends Model
{
    public $fillable = [
      'user_id',
      'order_no',
      'client_phone',
      'content',
    ];
}
