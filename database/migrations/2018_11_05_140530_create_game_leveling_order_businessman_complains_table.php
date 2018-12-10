<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderBusinessmanComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_businessman_complains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user_id')->comment('投诉商户ID');
            $table->integer('to_user_id')->comment('接被投诉商户ID');
            $table->string('game_leveling_order_trade_no', 22)->comment('关联单号');
            $table->decimal('amount')->comment('要求赔偿金额');
            $table->text('remark')->comment('备注');
            $table->integer('status')->comment('状态');
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
        Schema::dropIfExists('game_leveling_order_businessman_complains');
    }
}
