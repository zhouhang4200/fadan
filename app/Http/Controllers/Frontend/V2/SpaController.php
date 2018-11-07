<?php

namespace App\Http\Controllers\Frontend\V2;

use App\Http\Controllers\Controller;

class SpaController extends Controller
{
    function index()
    {
        return view('frontend.spa');
    }
}