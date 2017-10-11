<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_login_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_user_id')->unsigned()->comment('用户id');
            $table->bigInteger('ip')->comment('登录ip');
            $table->integer('city_id')->nullable()->comment('城市id');
            $table->string('remark')->nullable()->comment('备注');
            $table->text('detail')->nullable()->comment('详情');
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
        Schema::dropIfExists('admin_login_histories');
    }
}
