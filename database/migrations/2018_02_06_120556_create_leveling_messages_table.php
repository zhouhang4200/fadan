<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelingMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leveling_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用戶ID');
            $table->string('order_no', 22)->comment('订单号');
            $table->date('date')->comment('订单号');
            $table->string('contents', 1000)->comment('留言内容');
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
        Schema::dropIfExists('leveling_messages');
    }
}
