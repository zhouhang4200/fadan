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
//        $this->call(PlatfromAssetTableSeeder::class);
//        $this->call(AdminUserTableSeeder::class);
//        $this->call(PlatformAmountFlowSeeder::class);
        $this->call(GameLevelingOrderSeeder::class);
    }
}
