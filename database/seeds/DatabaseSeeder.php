<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PlatfromAssetTableSeeder::class);
        $this->call(AdminUserTableSeeder::class);

        \Illuminate\Support\Facades\DB::table('paltform_amount_flows')
            ->insert([
                'user_id' => 12,
                'admin_user_id' => 0,
                'trade_type' => 5,
                'trade_subtype' => 51,
                'trade_no' => '2018090517284900000042',
                'fee' => 100,
                'remark' => '安全保证金支出',
                'amount' => 100,
                'managed' => 100000,
                'balance' => 1000,
                'frozen' => 192,
                'total_recharge' => 5000,
                'total_withdraw' => 11112,
                'total_consume' => 15252,
                'total_refund' => 522,
                'total_trade_quantity' => 199,
                'total_trade_amount' => 6000,
                'created_at' => '2018-12-12 12:23:36',
                'flowable_type' => 'App\Models\Order',
                'flowable_id' => 1,
            ]);
    }
}
