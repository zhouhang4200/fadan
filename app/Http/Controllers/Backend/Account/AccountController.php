<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
    	$users = User::where('pid', 0)->latest('id')->paginate(config('frontend.page'));

    	return view('backend.account.index', compact('users'));
    }
}
