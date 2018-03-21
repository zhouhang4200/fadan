<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAutoMarkupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_auto_markups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('主账号');
            $table->decimal('markup_amount', 10, 2)->unsigned()->comment('发单价');
            $table->integer('markup_time')->unsigned()->comment('加价开始时间，单位分');
            $table->tinyInteger('markup_type')->unsigned()->default(0)->comment('加价类型, 0绝对值， 1百分比, 默认 0');
            $table->decimal('markup_money')->unsigned()->comment('增加金额，单位元');
            $table->integer('markup_frequency')->unsigned()->comment('加价频率');
            $table->integer('markup_number')->unsigned()->default(0)->comment('加价次数, 0为无限次');
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
        Schema::dropIfExists('order_auto_markups');
    }
}
