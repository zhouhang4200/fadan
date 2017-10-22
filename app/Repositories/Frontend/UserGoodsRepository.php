<?php
namespace App\Repositories\Frontend;

use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Goods;


class UserGoodsRepository
{
    /**
     * @param $serviceId
     * @param $gameId
     * @param $foreignGoodsId
     * @param int $pageSize
     * @return mixed
     */
    public function getList($serviceId, $gameId, $foreignGoodsId,$pageSize = 20)
    {
        $dataList = Goods::where('user_id', Auth::user()->getPrimaryUserId())
            ->with(['game', 'service'])
            ->when(!empty($serviceId), function ($query) use ($serviceId) {
                return $query->where('service_id', $serviceId);
            })
            ->when(!empty($gameId), function ($query) use ($gameId) {
                return $query->where('game_id', $gameId);
            })
            ->when(!empty($foreignGoodsId), function ($query) use ($foreignGoodsId) {
                return $query->where('foreign_goods_id', '>=', $foreignGoodsId);
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }

}
