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

    public static function rules()
    {
        return [
            'region' => 'required',
            'serve' => 'required',
            'role' => 'required',
            'game_leveling_type' => 'required',
            'account' => 'required',
            'password' => 'required',
            'game_leveling_title' => 'required',
            'game_leveling_instructions' => 'required',
            'game_leveling_requirements' => 'required',
            'game_leveling_amount' => 'required|after_or_equal:5',
            'game_leveling_day' => 'required|between:0,30',
            'game_leveling_hour' => 'required|between:1,24',
            'security_deposit' => 'required|after_or_equal:5',
            'efficiency_deposit' => 'required|after_or_equal:5',
            'user_phone' => 'required:numeric',
            'user_qq' => 'required:numeric',
            'client_phone' => 'required:numeric',
        ];
    }

    public static function messages()
    {
        return [
            'region.required' => '请选择区',
            'serve.required' => '请选择服',
            'role.required' => '请填写角色名',
            'game_leveling_type.required' => '请选择代练类型',
            'account.required' => '请填写账号',
            'password.required' => '请填写密码',
            'game_leveling_title.required' => '请填写代练标题',
            'game_leveling_instructions.required' => '请填写代练说明',
            'game_leveling_requirements.required' => '请填写代练要求',
            'game_leveling_amount.required' => '请填写代练价格',
            'game_leveling_day.required' => '请填写代练天数',
            'game_leveling_hour.required' => '请填写代练小明',
            'security_deposit.required' => '请填写安全保证金',
            'efficiency_deposit.required' => '请填写效率保证金',
            'user_phone.required' => '请选择商户电话',
            'user_qq.required' => '请选择商户QQ',
            'client_phone.required' => '请填写玩家联系方式',
        ];
    }
}
