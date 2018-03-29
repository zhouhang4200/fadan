<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('子员工id');
            $table->integer('parent_id')->unsigned()->comment('主id');
            $table->string('name')->comment('账号');
            $table->string('username')->nullable()->default('')->comment('账号昵称');
            $table->integer('complete_order_count')->default(0)->unsigned()->comment('已结算单数');
            $table->decimal('complete_order_amount', 10, 4)->default(0)->comment('已结算发单金额');
            $table->integer('revoke_order_count')->default(0)->unsigned()->comment('已撤销单数');
            $table->integer('arbitrate_order_count')->unsigned()->default(0)->comment('已仲裁单数');
            $table->decimal('profit', 10, 4)->default(0)->comment('利润');
            $table->date('date')->comment('数据日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_statistics');
    }
}
