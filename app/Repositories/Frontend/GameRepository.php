<?php
namespace App\Repositories\Frontend;

use App\Models\Game;
use App\Models\GoodsTemplate;
use App\Models\Service;
use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Goods;


class GameRepository
{
    /**
     * 可用的游戏
     * @return mixed
     */
    public function available()
    {
        return Game::where('status', 1)->pluck('name', 'id');
    }

    /**
     * 根据服务ID 来获取游戏列表
     * @param $serviceId
     * @return mixed
     */
    public function availableByServiceId($serviceId)
    {
        return GoodsTemplate::leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
            ->where('goods_templates.status', 1)
            ->where('service_id', $serviceId)
            ->pluck('games.name', 'games.id');
    }
}
