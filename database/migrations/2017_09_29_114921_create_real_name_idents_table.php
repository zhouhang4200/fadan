<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealNameIdentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_name_idents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('license_name')->comment('营业执照名称');
            $table->string('license_number')->comment('营业执照号码');
            $table->string('corporation')->comment('法人代表');
            $table->string('identity_card')->comment('身份证');
            $table->string('phone_number')->comment('电话号码');
            $table->string('license_picture')->comment('执照照片');
            $table->string('front_card_picture')->comment('身份证前照');
            $table->string('back_card_picture')->comment('身份证后照');
            $table->string('hold_card_picture')->comment('身份证手持照');
            $table->string('message')->nullable()->comment('审核不通过信息');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('审核状态,0：审核中，1审核通过，2审核不通过');
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
        Schema::dropIfExists('real_name_idents');
    }
}
