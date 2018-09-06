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
            $table->string('phone');
            $table->string('nickname');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('online')->default(0)->comment('是否在线：0 否 1 是');
            $table->tinyInteger('type')->default(1)->comment('1 接单 2 发单');
            $table->tinyInteger('leveling_type')->default(2)->comment('1 接单 2 发单');
            $table->string('username')->nullable()->comment('昵称');
            $table->tinyInteger('age')->unsigned()->nullable()->comment('年龄');
            $table->string('wechat')->nullable()->comment('微信');
            $table->string('qq')->nullable()->comment('qq');
            $table->string('store_wang_wang')->nullable()->comment('旺旺号');
            $table->string('voucher')->nullable()->comment('头像');
            $table->string('api_token')->nullable()->comment('api_token');
            $table->string('app_id')->nullable()->comment('app_id');
            $table->string('app_secret')->nullable()->comment('app_secret');
            $table->string('remark', 100)->nullable()->comment('备注');
            $table->rememberToken();
            $table->timestamp('deleted_at')->nullable()->comment('软删除字段');
            $table->tinyInteger('status')->default(0)->unsigned()->comment('状态：禁用1， 默认，0');
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
