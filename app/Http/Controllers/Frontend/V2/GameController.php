<?php

namespace App\Http\Controllers\Frontend\V2;

use App\Http\Controllers\Controller;
use App\Models\Game;


/**
 * 游戏
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class GameController extends Controller
{
    public function index()
    {
        return Game::get(['id', 'name']);
    }
}