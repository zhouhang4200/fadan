<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Api\App\OrderRepository;
use App\Repositories\Api\App\OrderChargeRepository;

class TestController extends Controller
{
    public function index(Request $request)
    {
        // $order = OrderRepository::detail('');

        dump(json_encode(['account' => '阿基里斯快递费']));

    }
}
