<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingMessage extends Model
{
    public $fillable = [
      'user_id',
      'order_no',
      'contents',
      'third_order_no',
      'foreign_order_no',
      'date',
      'send_time',
      'third',
    ];
}
