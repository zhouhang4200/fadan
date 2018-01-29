<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperateToOrderNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_notices', function (Blueprint $table) {
            $table->string('operate')->default('')->after('status')->comment('第三方操作');
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
            $table->dropColumn('operate');
        });
    }
}
