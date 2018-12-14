<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Models\PlatformAmountFlow::class, function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 24),
        'admin_user_id' => $faker->numberBetween(1, 24),
        'trade_type' => $faker->numberBetween(1, 8),
        'trade_subtype' => $faker->numberBetween(100, 890),
        'trade_no' => '20180905172849000'.$faker->numberBetween(1000, 9999),
        'fee' => $faker->numberBetween(100, 500),
        'remark' => '安全保证金支出',
        'amount' => $faker->numberBetween(100, 890),
        'managed' => $faker->numberBetween(10000, 89000),
        'balance' => $faker->numberBetween(10000, 80090),
        'frozen' => $faker->numberBetween(100, 890),
        'total_recharge' => $faker->numberBetween(10000, 89000),
        'total_withdraw' => $faker->numberBetween(100, 890),
        'total_consume' => $faker->numberBetween(1000, 8900),
        'total_refund' => $faker->numberBetween(100, 890),
        'total_trade_quantity' => $faker->numberBetween(100, 890),
        'total_trade_amount' => $faker->numberBetween(1000, 8900),
        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'flowable_type' => 'App\Models\Order',
        'flowable_id' => 1,
    ];
});

$factory->define(App\Models\GameLevelingOrder::class, function (Faker\Generator $faker) {
    return [
        'trade_no' => '201810291658090000'.$faker->numberBetween(1037, 9999),
        'source_amount' => $faker->numberBetween(100, 200),
        'status' => $faker->numberBetween(1, 24),
        'channel_order_trade_no' => '201810291658090000'.$faker->numberBetween(1037, 9999),
        'channel_order_status' => $faker->numberBetween(1,8),
        'platform_id' => $faker->numberBetween(1,5),
        'platform_trade_no' => '201810291658090000'.$faker->numberBetween(1037, 9999),
        'game_id' => 1,
        'repeat' => 0,
        'amount' => $faker->numberBetween(10, 100),
        'security_deposit' => $faker->numberBetween(1, 100),
        'efficiency_deposit' => $faker->numberBetween(1, 100),
        'poundage' => $faker->numberBetween(0, 10),
        'source' => $faker->numberBetween(1, 5),
        'user_id' => $faker->numberBetween(1, 15),
        'parent_user_id' => $faker->numberBetween(1, 15),
        'take_user_id' => $faker->numberBetween(1, 15),
        'take_parent_user_id' => $faker->numberBetween(1, 15),
        'top' => $faker->numberBetween(0, 1),
        'game_region_id' => $faker->numberBetween(1, 60),
        'game_server_id' => $faker->numberBetween(1, 60),
        'game_leveling_type_id' => $faker->numberBetween(1, 5),
        'day' => $faker->numberBetween(1, 10),
        'hour' => $faker->numberBetween(1, 24),
        'title' => '代练游戏下单，王者荣耀黄金5-黄金1',
        'game_account' => '游戏账号',
        'game_password' => '游戏密码',
        'game_role' => '游戏角色',
        'customer_service_name' => ['小花', '小明', '小红'][$faker->numberBetween(0, 2)],
        'seller_nick' => ['小花', '小明', '小红'][$faker->numberBetween(0, 2)],
        'buyer_nick' => ['小花', '小明', '小红'][$faker->numberBetween(0, 2)],
        'pre_sale' => ['小花', '小明', '小红'][$faker->numberBetween(0, 2)],
        'take_order_password' => '',
        'price_increase_step' => '',
        'price_ceiling' => '',
        'take_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'top_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'apply_complete_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'complete_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
    ];
});