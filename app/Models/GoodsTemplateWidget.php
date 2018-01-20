<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class GoodsTemplateWidget extends Model
{
    use RevisionableTrait;

    /**
     * 开启监听
     * @var bool
     */
    protected $revisionCreationsEnabled = true;

    /**
     * 自动清除记录
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * 保存多少条记录
     * @var int
     */
    protected $historyLimit = 50000;

    /**
     * @var array
     */
    public $fillable = [
        'goods_template_id',
        'field_display_name',
        'field_parent_id',
        'field_type',
        'field_name',
//        'field_value',
        'field_default_value',
        'field_required',
        'field_sortord',
        'created_admin_user_id',
        'updated_admin_user_id',
        'display_form',
        'help_text',
        'verify_rule',
    ];

    /**
     * 对应选项的值
     */
    public function values()
    {
        return $this->hasMany(GoodsTemplateWidgetValue::class, 'goods_template_widget_id', 'id');
    }

    /**
     * 用户设置的对应选项的值
     */
    public function userValues()
    {
        return $this->hasMany(GoodsTemplateWidgetValue::class, 'goods_template_widget_id', 'id');
    }
}
