<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nick_name')->nullable()->after('name')->comment('昵称');
            $table->tinyInteger('age')->unsigned()->nullable()->after('nick_name')->comment('年龄');
            $table->string('wechat')->nullable()->after('age')->comment('微信');
            $table->string('wangwang')->nullable()->after('wechat')->comment('旺旺号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nick_name');
            $table->dropColumn('age');
            $table->dropColumn('wechat');
            $table->dropColumn('wangwang');
        });
    }
}
