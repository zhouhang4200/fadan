<?php

use Illuminate\Database\Seeder;

class GameLevelingOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\GameLevelingOrder::class, 100000)->create();
    }
}
