<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', '22')->comment('订单号');
            $table->decimal('amount', 18, 4)->comment('退款金额');
            $table->tinyInteger('auditor')->comment('审核人 1 系统 2 人工');
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
        Schema::dropIfExists('refunds_records');
    }
}
