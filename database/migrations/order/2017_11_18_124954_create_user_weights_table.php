<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_weights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('weight')->comment('用户权重');
            $table->integer('less_than_six_percent')->default(0)->comment('小于6元订单，订单数量大于等于50单，增加的权重百分比');
            $table->integer('success_percent')->default(0)->comment('成功订单大于平台平均值，增加的权重百分比');
            $table->integer('use_time_percent')->default(0)->comment('订单用时小于平台平均值，增加的权重百分比');
            $table->integer('manual_percent')->comment('手动增加权重的百分比');
            $table->dateTime('start_date')->nullable()->comment('手动增加的权重-开始时间');
            $table->dateTime('end_date')->nullable()->comment('手动增加的权重-结束时间');
            $table->integer('created_admin_user_id')->comment('添加的管理员');
            $table->integer('updated_admin_user_id')->comment('更新的管理员');
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
        Schema::dropIfExists('user_weights');
    }
}
