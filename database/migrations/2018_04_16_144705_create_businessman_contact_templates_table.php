<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessmanContactTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businessman_contact_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('商户ID');
            $table->integer('game_id')->comment('game_id');
            $table->string('name')->comment('模板名字');
            $table->string('content')->comment('内容');
            $table->tinyInteger('type')->comment('1 联系电话 2 联系QQ');
            $table->tinyInteger('status')->comment('status');
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
        Schema::dropIfExists('businessman_contact_templates');
    }
}
