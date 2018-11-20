<?php

namespace App\Http\Controllers\Frontend\V2;

use App\Http\Controllers\Controller;

/**
 * Class SpaController
 * @package App\Http\Controllers\Frontend\V2
 */
class SpaController extends Controller
{
    /**
     * SpaController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if ( ! in_array(request()->getPathInfo(), ['/login', '/register'])) {

            return $this->middleware('auth');

        }
    }

    /**
     * @return mixed
     */
    function index()
    {
        return view('frontend.spa');
    }
}