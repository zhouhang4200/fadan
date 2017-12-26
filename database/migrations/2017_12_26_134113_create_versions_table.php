<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('number')->comment('版本号');
            $table->string('current_number')->comment('最新版本号');
            $table->integer('forced_update_id')->comment('强制更新最低版本id。关联本表id，用来做版本比较');
            $table->tinyInteger('forced_update')->default(0)->comment('强制更新：0.否 1.是');
            $table->string('remark')->default('')->comment('备注');
            $table->timestamps();
            $table->unique(['name', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('versions');
    }
}
