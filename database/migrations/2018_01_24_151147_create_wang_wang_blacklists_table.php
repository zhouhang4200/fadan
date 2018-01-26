<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWangWangBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wang_wang_blacklists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_user_id')->comment('添加管理员');
            $table->string('wang_wang')->comment('用户旺旺号');
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
        Schema::dropIfExists('wang_wang_blacklists');
    }
}
