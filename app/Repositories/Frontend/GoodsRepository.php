<?php
namespace App\Repositories\Frontend;

use DB;
use Auth;
use App\Models\Goods;

/**
 * Class GoodsRepository
 * @package App\Repositories\Frontend
 */
class GoodsRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function getTemplateIdByGoodsId($id)
    {
        return Goods::where('id', $id)->value('goods_template_id');
    }
}
