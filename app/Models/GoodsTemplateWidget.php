<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsTemplateWidget extends Model
{
    public $fillable = [
        'goods_template_id',
        'filed_display_name',
        'filed_parent_id',
        'filed_type',
        'filed_name',
        'filed_value',
        'filed_default_value',
        'filed_required',
        'filed_sort',
    ];
}
