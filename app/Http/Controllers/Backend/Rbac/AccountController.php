<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
    	$users = User::where('parent_id', 0)->latest('id')->paginate(config('frontend.page'));

    	return view('backend.rbac.account.index', compact('users'));
    }
}
