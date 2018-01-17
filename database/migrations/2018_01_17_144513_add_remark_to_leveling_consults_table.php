<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarkToLevelingConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leveling_consults', function (Blueprint $table) {
            $table->string('remark')->default('')->after('complete')->comment('客服仲裁回传说明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leveling_consults', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
}
