<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlatformAsset;

class PlatformAssetController extends Controller
{
    public function index()
    {
        $platformAsset = PlatformAsset::find(1);
        return view('backend.finance.platform-asset.index', compact('platformAsset'));
    }
}
