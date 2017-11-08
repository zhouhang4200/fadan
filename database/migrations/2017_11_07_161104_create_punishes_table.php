<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punishes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->string('order_id')->comment('关联订单号');
            $table->integer('user_id')->comment('用户id');
            $table->decimal('money', 12, 4)->comment('缴款金额');
            $table->tinyInteger('type')->comment('支付状态,0,未支付,1,已支付');
            $table->timestamp('deadline')->comment('缴款最后期限');
            $table->text('remark')->comment('备注');
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
        Schema::dropIfExists('punishes');
    }
}
