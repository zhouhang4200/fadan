<?php

namespace App\Extensions\Order\ForeignOrder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 淘宝订单处理
 * Class TmallForeignOrder
 * @package App\Extensions\Order\ForeignOrder
 */
class TmallForeignOrder extends Controller
{

    public function outputOrder()
    {
        // 根据商品ID匹配找到对应的配置，没有则直接放弃不处理


    }

}
