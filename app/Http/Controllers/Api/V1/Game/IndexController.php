<?php

namespace App\Http\Controllers\Api\V1\Game;

use App\Models\Game;
use App\Http\Controllers\Api\ApiController;

/**
 * Class IndexController
 * @package App\Http\Controllers\OpenApi\Game
 */
class IndexController extends ApiController
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->success(Game::all());
    }
}
