<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 组件类型
 * Class WidgetType
 * @package App\Models
 */
class WidgetType extends Model
{
    public $fillable = [
      'name',
      'type',
      'display_name',
    ];
}
