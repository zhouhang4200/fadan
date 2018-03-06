<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSteamStorePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steam_store_prices', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id')->unsigned()->comment('账号');
			$table->decimal('clone_price', 10, 4)->nullable()->comment('密价');
			$table->string('username')->comment('密价人');
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
        Schema::dropIfExists('steam_store_prices');
    }
}
