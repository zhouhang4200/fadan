<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTemplateWidgetValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_template_widget_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('goods_template_widget_id', false, true)->comment('关联goods_template_widget 中ID');
            $table->integer('user_id', false, true)->comment('如果组件支持用用户义组件值，则此处为用户ID');
            $table->integer('parent_id', false, true)->comment('组件的父级ID,只有在下拉组件时有意义');
            $table->string('field_name', 60)->comment('组件定义的英文名');
            $table->string('field_value', 60)->comment('组件的值');
            $table->string('field_content', 600)->comment('组件值对应的内容');
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
        Schema::dropIfExists('goods_template_widget_values');
    }
}
