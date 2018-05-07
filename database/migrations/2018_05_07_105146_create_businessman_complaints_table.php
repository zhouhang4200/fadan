<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessmanComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businessman_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('complaint_primary_user_id')->comment('投诉商户主ID');
            $table->integer('be_complained_primary_user_id')->comment('接被投诉商户主ID');
            $table->string('order_no', 22)->comment('关联单号');
            $table->decimal('amount')->comment('赔偿金额');
            $table->text('remark')->comment('备注');
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
        Schema::dropIfExists('businessman_complaints');
    }
}
