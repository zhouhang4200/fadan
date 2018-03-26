<?php

namespace App\Http\Controllers\Frontend\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StationController extends Controller
{
    public function index(Request $request)
    {
    	$rbacGroups = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())
            ->whereHas('permissions')
            ->paginate(config('frontend.page'));

        return view('frontend.user.rbacgroup.index', compact('rbacGroups'));
    }
}
