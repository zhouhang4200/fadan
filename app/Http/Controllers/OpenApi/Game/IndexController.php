<?php

namespace App\Http\Controllers\OpenApi\Game;

use App\Models\Game;
use App\Http\Controllers\Controller;

/**
 * Class IndexController
 * @package App\Http\Controllers\OpenApi\Game
 */
class IndexController extends Controller
{
    public function index()
    {
        return response([Game::all()], 200);
    }
}
