<?php

namespace App\Http\Controllers\Api\V1\Game;

use App\Http\Controllers\Api\ApiController;
use App\Models\GameServer;

/**
 * Class ServerController
 * @package App\Http\Controllers\Api\V1\Game
 */
class ServerController extends ApiController
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->success(GameServer::all());
    }
}
