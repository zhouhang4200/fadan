<?php
return [
  'algorithm' => [
      // 小6元
      App\Extensions\Weight\Algorithm\OrderSix::class,
      // 成功订单
      App\Extensions\Weight\Algorithm\OrderSuccess::class,
      // 时间
      App\Extensions\Weight\Algorithm\OrderUseTime::class,
  ]
];