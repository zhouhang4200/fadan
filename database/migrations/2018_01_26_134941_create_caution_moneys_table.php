<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 保证金表
 * Class CreateCautionMoneysTable
 */
class CreateCautionMoneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caution_moneys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->comment('保证金单据号');
            $table->tinyInteger('type')->comment('保证金类型，见config 文件 cautionmoney');
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('amount')->comment('保证金金额');
            $table->tinyInteger('status')->comment('状态 1 已交 2 已退 3 已扣');
            $table->string('remark')->comment('备注');
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
        Schema::dropIfExists('caution_moneys');
    }
}
