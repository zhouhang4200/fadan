<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\UserAsset;

class AssetController extends Controller
{
    public function index()
    {
        $asset = UserAsset::find(Auth::user()->id);
        return view('frontend.finance.asset.index', compact('asset'));
    }
}
