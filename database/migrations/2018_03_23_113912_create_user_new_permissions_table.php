<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNewPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_permission_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('主账号或子账号ID');
            $table->integer('new_permission_id')->unsigned()->comment('权限ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_new_permission');
    }
}
