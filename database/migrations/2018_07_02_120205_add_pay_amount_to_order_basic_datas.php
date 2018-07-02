<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayAmountToOrderBasicDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_basic_datas', function (Blueprint $table) {
            $table->decimal('pay_amount', 10, 4)->after('price')->comment('完成验收商家支出的代练费，即状态为20支出的代练费');
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
            $table->dropColumn('pay_amount');
        });
    }
}
