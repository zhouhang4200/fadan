<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoucherToPunishes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punishes', function (Blueprint $table) {
            $table->string('voucher')->default('')->after('deadline')->comment('凭证照片');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punishes', function (Blueprint $table) {
            $table->dropColumn('voucher');
        });
    }
}
