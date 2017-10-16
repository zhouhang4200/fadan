<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAccountController extends Controller
{
    public function index()
    {
    	$users = AdminUser::latest('id')->paginate(config('backend.page'));

    	return view('backend.account.admin.index', compact('users'));
    }
}
