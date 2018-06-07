<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTransferAccountInfo extends Model
{
    public $fillable = [
      'user_id',
      'name',
      'bank_name',
      'bank_card',
      'alipay',
    ];
}
