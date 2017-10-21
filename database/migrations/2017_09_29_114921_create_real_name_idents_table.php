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
            $table->string('license_name')->nullable()->comment('营业执照名称');
            $table->string('license_number')->nullable()->comment('营业执照号码');
            $table->string('corporation')->nullable()->comment('法人代表');
            $table->string('identity_card')->nullable()->comment('身份证');
            $table->string('phone_number')->nullable()->comment('电话号码');
            $table->string('license_picture')->nullable()->comment('执照照片');
            $table->string('front_card_picture')->nullable()->comment('身份证前照');
            $table->string('back_card_picture')->nullable()->comment('身份证后照');
            $table->string('hold_card_picture')->nullable()->comment('身份证手持照');
            $table->string('bank_open_account_picture')->nullable()->comment('银行开户许可照片');
            $table->string('agency_agreement_picture')->nullable()->comment('代办协议照片');
            $table->string('message')->nullable()->comment('审核不通过信息');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('1个人,2公司');
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
