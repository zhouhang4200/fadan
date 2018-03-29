<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsSendRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_send_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('发送商户ID/主账号或子账号');
            $table->string('order_no')->comment('关联订单号');
            $table->string('foreign_order_no', 100)->nullable()->comment('外部订单号');
            $table->string('client_phone')->comment('客户手机号，接收短信的C端用户');
            $table->string('content')->comment('短信发送内容');
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
        Schema::dropIfExists('sms_send_records');
    }
}
