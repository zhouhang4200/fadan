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
            $table->string('name')->comment('名称');
            $table->string('alias')->comment('别名');
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
