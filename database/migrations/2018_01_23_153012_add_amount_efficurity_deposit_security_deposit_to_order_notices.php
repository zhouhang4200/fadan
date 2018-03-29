<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountEfficurityDepositSecurityDepositToOrderNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_notices', function (Blueprint $table) {
            $table->decimal('amount', 10, 4)->unsigned()->default(0)->comment('订单金额');
            $table->decimal('security_deposit')->unsigned()->default(0)->comment('安全保证金金额');
            $table->decimal('efficiency_deposit')->unsigned()->default(0)->comment('效率保证金金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_notices', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('security_deposit');
            $table->dropColumn('efficiency_deposit');
        });
    }
}
