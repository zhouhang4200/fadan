<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rbac_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('user_id')->unsigned()->comment('user_id');
            $table->tinyInteger('to_user_id')->unsigned()->comment('子id');
            $table->string('name')->comment('名称');
            $table->string('permission_ids')->comment('权限id组合');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('rbac_groups');
    }
}
