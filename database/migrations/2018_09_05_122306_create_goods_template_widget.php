<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTemplateWidget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_template_widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goods_template_id')->comment('模版ID');
            $table->string('field_display_name')->comment('字段显示名称');
            $table->unsignedInteger('field_parent_id')->comment('父级ID,只对下拉项生效');
            $table->string('field_name')->comment('字段名称');
            $table->string('field_name_alias')->comment('字段名称');
            $table->unsignedInteger('field_type')->comment('字段类型');
            $table->text('field_value')->comment('字段值');
            $table->string('field_default_value')->comment('字段默认值');
            $table->unsignedTinyInteger('field_required')->comment('字段是否必填  1是 2否');
            $table->unsignedInteger('field_sortord')->comment('字段排序');
            $table->string('verify_rule')->comment('验证规则');
            $table->unsignedInteger('created_admin_user_id')->comment('创建人');
            $table->unsignedInteger('updated_admin_user_id')->comment('更新人');
            $table->string('display_form')->comment('组件展示形式 1：独占一行 2：两个组件显示在一行 3：三个组件显示在一行');
            $table->string('display_api')->comment('组件是否在api接口展示');
            $table->string('display')->comment('组件是否在页面展示，因为有些字某不需要用户输入只用于信息记录');
            $table->string('help_text')->comment('用户输入内容时的提示文字');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_template_widgets');
    }
}
