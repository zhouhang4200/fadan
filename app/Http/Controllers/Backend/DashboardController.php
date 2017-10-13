<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('backend.dashboard.index');
    }
}