<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsTemplateWidgetValue extends Model
{
    public $fillable = [
      'goods_template_widget_id',
      'user_id',
      'parent_id',
      'field_name',
      'field_value',
      'field_value',
      'field_content',
    ];

    public function getFieldValueAttribute($value)
    {
    	return trim($value);
    }
}
