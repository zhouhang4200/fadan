<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->timeStamp('date')->comment('什么时候的数据');
            $table->decimal('stock_trusteeship', 10, 4)->unsigned()->comment('库存托管');
            $table->decimal('stock_transaction', 10, 4)->unsigned()->comment('库存交易');
            $table->decimal('transfer_transaction', 10, 4)->unsigned()->comment('转单市场');
            $table->decimal('slow_recharge', 10, 4)->unsigned()->comment('慢充');
            $table->decimal('order_market', 10, 4)->unsigned()->comment('订单集市');
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
        Schema::dropIfExists('day_datas');
    }
}
