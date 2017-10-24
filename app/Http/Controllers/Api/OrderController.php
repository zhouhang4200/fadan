<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

class OrderController extends Controller
{
    /**
     *
     */
    public function create()
    {
        ForeignOrderFactory::choose('kamen')->outputOrder([]);
    }
}
