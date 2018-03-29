<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('new_module_id')->unsigned()->comment('模块ID，权限属于哪个模块');
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
        Schema::dropIfExists('new_permissions');
    }
}
