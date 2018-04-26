<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('no', 30)->comment('单号');
            $table->tinyInteger('type')->comment('类型');
            $table->tinyInteger('status')->default(1)->comment('状态 1.待扣款 2.已扣款 3.待退款 4.已退款');
            $table->decimal('amount', 15, 2)->comment('押金金额');
            $table->string('remark', 200)->nullable()->default(null)->comment('备注');
            $table->unsignedInteger('created_by')->comment('创建人');
            $table->unsignedInteger('deduct_audited_by')->nullable()->default(null)->comment('扣款审核');
            $table->unsignedInteger('refunded_by')->nullable()->default(null)->comment('退款人');
            $table->unsignedInteger('refunded_audited_by')->nullable()->default(null)->comment('退款审核');
            $table->timestamps();

            $table->unique('no');
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
