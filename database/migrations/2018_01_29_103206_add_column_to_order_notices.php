<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrderNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_notices', function (Blueprint $table) {
            $table->tinyInteger('child_third_status')->unisgned()->after('third_status')->comment('子状态，撤销、申诉中');
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
            $table->dropColumn('child_third_status');
        });
    }
}
