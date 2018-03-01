<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAreaNameToThirdAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('third_areas', function (Blueprint $table) {
            $table->string('area_name')->after('area_id')->comment('我们平台的区名字');
            $table->string('third_area_name')->after('third_area_id')->comment('第三方平台区名字');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('third_areas', function (Blueprint $table) {
            $table->dropColumn('area_name');
            $table->dropColumn('third_area_name');
        });
    }
}
