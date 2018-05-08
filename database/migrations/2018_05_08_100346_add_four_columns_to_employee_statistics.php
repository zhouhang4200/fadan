<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFourColumnsToEmployeeStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_statistics', function (Blueprint $table) {
            $table->integer('all_count')->unsigned()->comment('订单总计');
            $table->decimal('all_original_price', 12, 4)->comment('总来源价格');
            $table->decimal('all_price', 12, 4)->comment('总价格');
            $table->decimal('subtract_price', 12, 4)->comment('来源/价格差价');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_statistics', function (Blueprint $table) {
            //
        });
    }
}
