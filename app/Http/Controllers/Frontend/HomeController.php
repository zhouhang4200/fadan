<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$user = Auth::user();

    	$role = Role::where('name', 'manager')->first();

    	$role2 = Role::where('name', 'writer')->first();

    	$permission = Permission::where('name', 'add accounts')->first();

    	$permission2 = Permission::where('name', 'edit accounts')->first();

    	if (! $role) {

	    	Role::create(['name' => 'manager']);		
    	}

    	if (! $role2) {

    		Role::create(['name' => 'writer']);
    	}

    	if (! $permission) {

	    	Permission::create(['name' => 'add accounts']);
    	}

    	if (! $permission2) {

    		Permission::create(['name' => 'edit accounts']);
    	}


    	// $user->syncRoles(['manager', 'writer']);
    	// 
    	// $user->syncPermissions(['add accounts', 'edit accounts']);
    	
    	// $role->givePermissionTo(['add accounts', 'edit accounts']);
    	
    	$bool = $role->hasPermissionTo('add accounts'); // true

    	$bool1 = $user->hasAnyPermission(['add accounts', 'edit accounts', 'dd accounts']); // true

    	$bool2 = $user->can(['edit accounts', 'add accounts', 'as accounts']); // false

// dd($bool2);
        return view('frontend.index');
    }
}
