<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->comment('父id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->tinyInteger('user_type')->unsigned()->comment('父：1，子：2');
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
        Schema::dropIfExists('login_histories');
    }
}
