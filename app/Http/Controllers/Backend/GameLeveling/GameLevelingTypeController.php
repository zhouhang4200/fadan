<?php

namespace App\Http\Controllers\Backend\GameLeveling;

use App\Models\GameLevelingType;
use App\Http\Controllers\Controller;

/**
 * 游戏代练类型
 * Class GameLevelingTypeController
 * @package App\Http\Controllers\Backend\GameLeveling\Channel
 */
class GameLevelingTypeController extends  Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return response()->ajax(1, 'success', GameLevelingType::filter(request()->all())->get());
        }
    }

}