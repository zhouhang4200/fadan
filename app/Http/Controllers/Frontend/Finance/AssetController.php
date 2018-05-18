<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

/**
 * Class AssetController
 * @package App\Http\Controllers\Frontend\Finance
 */
class AssetController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $asset = Auth::user()->userAsset;
        return view('frontend.v1.finance.asset.index', compact('asset'));
    }
}
