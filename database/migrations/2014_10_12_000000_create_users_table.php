<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(0)->comment('父id');
            $table->string('name')->unique();
            $table->string('nickname');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('online')->default(0)->comment('是否在线：0 否 1 是');
            $table->tinyInteger('type')->default(1)->comment('1 接单 2 发单');
            $table->timestamp('deleted_at')->nullable()->comment('软删除字段');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
