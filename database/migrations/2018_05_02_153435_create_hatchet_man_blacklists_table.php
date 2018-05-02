<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHatchetManBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hatchet_man_blacklists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('商户主id');
            $table->string('hatchet_man_name')->comment('打手昵称');
            $table->string('hatchet_man_phone')->comment('打手电话');
            $table->string('hatchet_man_qq')->comment('打手QQ');
            $table->tinyInteger('third')->unsigned()->comment('第三方平台;1 91；2代练妈妈；3蚂蚁；4 373');
            $table->string('content', 500)->comment('备注信息');
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
        Schema::dropIfExists('hatchet_man_blacklists');
    }
}
