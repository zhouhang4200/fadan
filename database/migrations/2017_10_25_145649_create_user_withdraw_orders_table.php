<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWithdrawOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdraw_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no', 30)->comment('编号');
            $table->tinyInteger('status')->comment('状态：1.申请提现 2.提现成功 3.拒绝');
            $table->decimal('fee', 10, 4)->comment('金额');
            $table->unsignedInteger('creator_user_id')->comment('创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->string('remark')->comment('备注说明');
            $table->datetime('created_at');
            $table->datetime('updated_at');

            $table->unique('no');
            $table->index('status');
            $table->index('creator_user_id');
            $table->index('creator_primary_user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        /* 20180808
        ALTER TABLE `user_withdraw_orders`
            ADD COLUMN `bank_name`  varchar(50) NULL DEFAULT NULL COMMENT '提现银行' AFTER `updated_at`,
            ADD COLUMN `bank_card`  varchar(50) NULL DEFAULT NULL COMMENT '银行卡号' AFTER `bank_name`,
            ADD COLUMN `account_name`  varchar(50) NULL DEFAULT NULL COMMENT '账户人姓名' AFTER `bank_card`,

            ADD COLUMN `attach`  varchar(100) NULL DEFAULT NULL COMMENT '附件' AFTER `account_name`;

            ADD COLUMN `bill_id`  varchar(30) NULL DEFAULT NULL COMMENT '财务接口单号' AFTER `attach`,
            ADD COLUMN `bill_status`  tinyint NULL DEFAULT NULL COMMENT '财务接口办款结果 0.失败 1.成功' AFTER `bill_id`,
            ADD COLUMN `bill_user_name`  varchar(30) NULL DEFAULT NULL COMMENT '财务接口办款人' AFTER `bill_status`,
            ADD COLUMN `pay_account`  varchar(50) NULL DEFAULT NULL COMMENT '付款账号' AFTER `bill_user_name`,
            ADD COLUMN `pay_bank_full_name`  varchar(100) NULL DEFAULT NULL COMMENT '付款银行全称' AFTER `pay_account`,
            ADD COLUMN `transfer_detail`  varchar(1000) NULL DEFAULT NULL COMMENT '转款明细(json)' AFTER `pay_bank_full_name`,

            ADD UNIQUE INDEX `user_withdraw_orders_bill_id_index` (`bill_id`) USING BTREE ,;
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_withdraw_orders');
    }
}
