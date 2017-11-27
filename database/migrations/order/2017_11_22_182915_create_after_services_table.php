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
            $table->string('order_on')->comment('订单号');
            $table->integer('creator_user_id')->unsigned()->default(0)->comment('发单人ID');
            $table->decimal('creator_refund_amount')->default(0)->comment('给发单人退款金额');
            $table->string('creator_refund_remark')->nullable()->comment('给发单人退款备注');
            $table->integer('gainer_user_id')->unsigned()->default(0)->comment('接单人ID');
            $table->decimal('gainer_refund_amount')->default(0)->comment('给接单人退款金额');
            $table->string('gainer_refund_remark')->nullable()->comment('给接单人退款备注');
            $table->integer('auditing_admin_user_id')->unsigned()->default(0)->comment('审核管理员ID');
            $table->integer('confirm_admin_user_id')->unsigned()->default(0)->comment('确认管理员ID');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态 0 待审核 1 审核通过 2 审核拒绝 3 完成退款');
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
