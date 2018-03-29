<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelingConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leveling_consults', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('操作人id');
            $table->string('order_no')->comment('订单号');
            $table->decimal('amount', 10, 4)->comment('填写申请退还的代练金');
            $table->decimal('deposit', 10, 4)->comment('填写申请退还的双金');
            $table->decimal('api_amount', 10, 4)->default(0)->comment('接口回传退还的代练金，需要更新这个值');
            $table->decimal('api_deposit', 10, 4)->default(0)->comment('接口回传双金，需要更新这个值');
            $table->decimal('api_service', 10, 4)->default(0)->comment('接口回传手续费，需要更新这个值');
            $table->tinyInteger('consult')->unsigned()->default(0)->comment('撤销0，默认，1发单撤销，2接单撤销');
            $table->tinyInteger('complain')->unsigned()->default(0)->comment('撤销0，默认，1发单申诉，2接单申诉');
            $table->tinyInteger('complete')->unsigned()->default(0)->comment('0未完成，1已完成，当为1的时候此单表示为已撤销或已申诉要开始扣款');
            $table->string('revoke_message', 500)->default('')->comment('撤销原因');
            $table->string('complain_message', 500)->default('')->comment('申诉原因');
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
        Schema::dropIfExists('leveling_consults');
    }
}
