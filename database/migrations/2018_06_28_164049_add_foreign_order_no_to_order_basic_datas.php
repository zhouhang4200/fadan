<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignOrderNoToOrderBasicDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_basic_datas', function (Blueprint $table) {
            $table->string('foreign_order_no')->after('order_no')->comment('天猫订单号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_basic_datas', function (Blueprint $table) {
             $table->dropColumn('foreign_order_no');
        });
    }
}
