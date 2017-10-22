<?php
namespace App\Repositories\Frontend;

use App\Models\Game;
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
}
