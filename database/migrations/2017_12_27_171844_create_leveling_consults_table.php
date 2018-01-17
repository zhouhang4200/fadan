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
            $table->decimal('amount', 10, 4)->default(0)->comment('填写申请退还的代练金');
            $table->decimal('deposit', 10, 4)->default(0)->comment('填写申请退还的双金');
            $table->decimal('api_amount', 10, 4)->default(0)->comment('接口回传退还的代练金，需要更新这个值');
            $table->decimal('api_deposit', 10, 4)->default(0)->comment('接口回传双金，需要更新这个值');
            $table->decimal('api_service', 10, 4)->default(0)->comment('接口回传手续费，需要更新这个值');
            $table->tinyInteger('status')->default(0)->unsigned()->comment('申请仲裁：0未申请仲裁(撤销)，1 发单方申请仲裁, 2 接单方申请仲裁, 3 已仲裁, 4 已撤销');
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
