<?php

namespace App\Http\Controllers\Backend\Account;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAccountController extends Controller
{
    public function index()
    {
    	$adminUsers = AdminUser::paginate(config('backend.page'));

    	return view('backend.account.admin-index', compact('adminUsers'));
    }
}
