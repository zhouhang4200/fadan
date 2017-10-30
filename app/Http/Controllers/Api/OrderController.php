<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

class OrderController extends Controller
{

    public function create()
    {
        ForeignOrderFactory::choose('kamen')->outputOrder([]);
    }

    public function KamenOrder(Request $request)
    {
        return ForeignOrderFactory::choose('kamen')->outputOrder($request->data);
    }

    public function TmallOrder(Request $request)
    {
        return ForeignOrderFactory::choose('tmall')->outputOrder($request->data);
    }

    public function JdOrder(Request $request)
    {
        return ForeignOrderFactory::choose('jd')->outputOrder($request->data);
    }
    public function test(Request $request)
    {
        \Log::alert(json_encode($request->all()));
    }
}
