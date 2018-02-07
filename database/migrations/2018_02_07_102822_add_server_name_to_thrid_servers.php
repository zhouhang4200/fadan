<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServerNameToThridServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('third_servers', function (Blueprint $table) {
            $table->string('server_name')->after('server_id')->comment('我们平台服名字');
            $table->string('third_server_name')->after('third_server_id')->comment('第三方服名字');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('third_servers', function (Blueprint $table) {
            $table->dropColumn('server_name');
            $table->dropColumn('third_server_name');
        });
    }
}
