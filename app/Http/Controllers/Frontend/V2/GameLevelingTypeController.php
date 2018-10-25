<?php

namespace App\Http\Controllers\Frontend\V2;

use App\Http\Controllers\Controller;
use App\Models\GameLevelingType;


/**
 * 游戏代练类型
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class GameLevelingTypeController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return GameLevelingType::where('game_id', request('game_id'))->get(['id', 'name']);
    }
}