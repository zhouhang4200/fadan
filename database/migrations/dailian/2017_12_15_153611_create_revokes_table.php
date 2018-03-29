<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevokesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revokes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_primary_user_id')->unsigned()->comment('发单商户主id');
            $table->integer('gainer_primary_user_id')->unsigned()->comment('接单商户主id');
            $table->string('order_no')->comment('订单id');
            $table->decimal('amount', 10, 4)->comment('申请退还的代练金');
            $table->decimal('deposit', 10, 4)->comment('申请退还的保证金');
            $table->tinyInteger('status')->unsigned()->comment('申请仲裁：0未申请仲裁，1 发单方申请仲裁, 2 接单方申请仲裁, 3 已仲裁');
            $table->string('revoke_message', 500)->comment('撤销原因');
            $table->string('complain_message', 500)->nullable()->comment('申诉原因');
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
        Schema::dropIfExists('revokes');
    }
}
