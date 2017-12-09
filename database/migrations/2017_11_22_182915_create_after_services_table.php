<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfterServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('after_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->integer('order_creator_user_id')->unsigned()->default(0)->comment('发单人ID');
            $table->integer('order_gainer_user_id')->unsigned()->default(0)->comment('接单人ID');
            $table->decimal('original_amount')->comment('订单原金额');
            $table->decimal('refund_amount')->default(0)->comment('退款金额');
            $table->integer('auditing_admin_user_id')->unsigned()->default(0)->comment('审核管理员ID');
            $table->integer('confirm_admin_user_id')->unsigned()->default(0)->comment('确认管理员ID');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态 0 待审核 1 审核通过 2 审核拒绝 3 完成退款');
            $table->string('remark')->nullable()->comment('发起退款说明');
            $table->string('auditing_remark')->nullable()->comment('审核说明');
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
        Schema::dropIfExists('after_services');
    }
}
