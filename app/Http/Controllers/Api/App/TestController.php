<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index(Request $request)
    {
        dd($request->data);
    }
}
