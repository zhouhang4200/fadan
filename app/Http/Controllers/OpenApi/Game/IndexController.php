<?php

namespace App\Http\Controllers\OpenApi\Game;

use App\Models\Game;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return response([Game::all()], 200);
    }
}
