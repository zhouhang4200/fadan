<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewRoleNewPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_permission_new_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('new_role_id')->unsigned()->comment('角色ID');
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
        Schema::dropIfExists('new_role_new_permission');
    }
}
