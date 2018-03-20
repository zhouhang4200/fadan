<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOurOperateToOrderNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_notices', function (Blueprint $table) {
            $table->string('our_operate')->default('')->after('operate')->comment('我们的操作');
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
            $table->dropColumn('our_operate');
        });
    }
}
