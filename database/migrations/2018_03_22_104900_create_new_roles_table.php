<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->unsigned()->comment('主账号ID, 默认为0，为0 表示是后台管理员创建的公用的角色，如果是主账号给自己拥有的权限分配的角色，则此user_id即为主账号ID');
            $table->string('name')->comment('角色英文名称');
            $table->string('alias')->comment('角色中文名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_roles');
    }
}
