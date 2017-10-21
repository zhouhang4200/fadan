<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsTemplateWidget extends Model
{
    public $fillable = [
        'goods_template_id',
        'field_display_name',
        'field_parent_id',
        'field_type',
        'field_name',
        'field_value',
        'field_default_value',
        'field_required',
        'field_sort',
    ];
}
