<?php

return [
    'platform' =>[
        1 => [
            'name' => '91平台', // 平台名称
            'user_id' => 8456, // 千手用户ID
            'receive' => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/qs/receiveOrder', // 接收订单地址
            'aes_key' => '45584685d8e4f5e8',
            'aes_iv' => '1234567891111152',
        ],
        3 => [
            'name' => '蚂蚁代练', // 平台名称
            'user_id' => 8737, // 千手用户ID
            'receive' => env('MAYI_API_URL', 'http://www.mayidailian.com/OpenApi/GateWay/index'), // 接收订单地址
            'aes_key' => '4l5846ssd8e4f5e8e4e2685',
            'aes_iv' => '1234567891111152',
        ],
        4 => [
            'name' => 'dd373', // 平台名称
            'user_id' => 8739, // 千手用户ID
            'receive' => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html?action=pushOrder&platformSign=9cd4100e4af146f487284bb18f190c59'), // 接收订单地址
            'aes_key' => '45xd46a5d8e4f5e8e4e268x',
            'aes_iv' => '1234567891111152',
        ],
       5 => [
           'name' => '丸子代练', // 平台名称
           'user_id' => env('MAYI_IN_TM_USER_ID', '8880'), // 千手用户ID
           'receive' => env('WANZI_API_URL', 'http://www.fulugou.net').'/tm/place-order', // 接收订单地址
           'aes_key' => '335ss6s8m8e4f5a8e2e2ls5',
           'aes_iv' => '1234567891111152',
       ],
    ]
];