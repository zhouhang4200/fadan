<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户设置存储
 * Class UserSetting
 * @package App\Models
 */
class UserSetting extends Model
{
    public $fillable = [
      'user_id',
      'option',
      'value',
    ];
}
