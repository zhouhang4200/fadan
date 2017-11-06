<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReceivingUserControl extends Model
{
    public $fillable = [
      'user_id',
      'other_user_id',
      'remark',
      'type',
    ];
}
