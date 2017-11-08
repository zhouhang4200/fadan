<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserReceivingUserControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_receiving_user_controls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->commnet('添加记录的用户ID');
            $table->integer('other_user_id')->comment('关联的用户ID');
            $table->tinyInteger('type')->comment('类型 1 白名单 2 黑名单');
            $table->string('remark')->comment('备注');
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
        Schema::dropIfExists('user_receiving_user_controls');
    }
}
