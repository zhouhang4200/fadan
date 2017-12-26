<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widget_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('组件名称');
            $table->tinyInteger('type')->comment('1输入框 2:下拉项 3:单选项 4:多行文本 5:复选框');
            $table->string('display_name')->comment('组件显示的名称');
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
        Schema::dropIfExists('widget_types');
    }
}
