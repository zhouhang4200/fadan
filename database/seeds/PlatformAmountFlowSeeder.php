<?php

use Illuminate\Database\Seeder;

class PlatformAmountFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\PlatformAmountFlow::class, 100000)->create();
    }
}
