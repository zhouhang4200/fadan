<?php

use Illuminate\Database\Seeder;

class PlatfromAssetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('platform_assets')->insert([
            'id'                   => 1,
            'amount'               => 0,
            'managed'              => 0,
            'balance'              => 0,
            'frozen'               => 0,
            'total_recharge'       => 0,
            'total_withdraw'       => 0,
            'total_consume'        => 0,
            'total_refund'         => 0,
            'total_trade_quantity' => 0,
            'total_trade_amount'   => 0,
            'updated_at'           => '2017-10-18',
        ]);
    }
}
