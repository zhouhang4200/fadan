<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeightRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weight_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->string('order_id')->comment('关联订单号');
            $table->integer('user_id')->comment('用户id');
            $table->integer('before_weight_value')->comment('用户扣除和增加之前的权重值');
            $table->tinyInteger('ratio')->comment('扣除或增加权重的系数，正负数，如-10，即为-10*0.01*权重值');
            $table->integer('after_weight_value')->comment('用户扣除和增加之后的权重值');
            $table->string('voucher')->comment('凭证照片');
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
        Schema::dropIfExists('weight_records');
    }
}
