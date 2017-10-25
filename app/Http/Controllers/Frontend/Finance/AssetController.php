<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AssetController extends Controller
{
    public function index()
    {
        $asset = Auth::user()->userAsset;
        return view('frontend.finance.asset.index', compact('asset'));
    }
}
