<?php
namespace App\Repositories\Backend;

use App\Models\Game;

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
