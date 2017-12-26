<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToGoodsTemplateWidgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_template_widgets', function (Blueprint $table) {
            $table->string('display_form')->comment('组件展示形式 1：独占一行 2：两个组件显示在一行 3：三个组件显示在一行');
            $table->string('display_api')->comment('组件是否在api接口展示');
            $table->string('display')->comment('组件是否在页面展示，因为有些字某不需要用户输入只用于信息记录');
            $table->string('help_text')->comment('用户输入内容时的提示文字');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_template_widgets', function (Blueprint $table) {
            //
        });
    }
}
