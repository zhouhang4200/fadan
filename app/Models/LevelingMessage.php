<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingMessage extends Model
{
    public $fillable = [
      'user_id',
      'order_no',
      'contents',
      'date',
      'send_time',
      'third',
    ];
}
