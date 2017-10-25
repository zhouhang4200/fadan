<?php
namespace App\Repositories\Api;

use App\Models\Goods;

class GoodsRepository
{
    static public function find($goodsId)
    {
        $goods = Goods::find($goodsId);
        return $goods;
    }
}
