<?php

namespace App\Http\Controllers\Api\V1\Game;

use App\Http\Controllers\Api\ApiController;
use App\Models\GameRegion;

/**
 * Class RegionController
 * @package App\Http\Controllers\OpenApi\Game
 */
class RegionController extends ApiController
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->success(GameRegion::all());
    }
}
