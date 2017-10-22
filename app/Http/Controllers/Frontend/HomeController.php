<?php

namespace App\Http\Controllers\Frontend;

use Auth, Weight;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:home.add.order|home.edit.order']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


//        return Weight::run([1]);
//     	$user = Auth::user();

//     	$role = Role::where('name', 'manager')->first();

//     	$role2 = Role::where('name', 'writer')->first();

//     	$permission = Permission::where('name', 'add accounts')->first();

//     	$permission2 = Permission::where('name', 'edit accounts')->first();

//     	$permission3 = Permission::where('name', 'delete accounts')->first();
// dd($role);
    	// if (! $role) {

	    	// \App\Models\Permission::create(['name' => 'manager1']);		
    	// }

    	// if (! $role2) {

    	// 	Role::create(['name' => 'writer']);
    	// }

    	// if (! $permission) {

	    // 	Permission::create(['name' => 'add accounts']);
    	// }

    	// if (! $permission2) {

    	// 	Permission::create(['name' => 'edit accounts']);
    	// }

    	// if (! $permission3) {
    	// 	Permission::create(['name' => 'delete accounts', 'alias' => '删除子账号']);
    	// }


    	// $user->syncRoles(['manager', 'writer']);
    	
    	// $user->syncPermissions(['add accounts', 'edit accounts']);
    	
    	// $role->givePermissionTo(['add accounts', 'edit accounts']);
    	
    	// $bool = $role->hasPermissionTo('add accounts'); // true

    	// $bool1 = $user->hasAnyPermission(['add accounts', 'edit accounts', 'dd accounts']); // true

    	// $bool2 = $user->can(['edit accounts', 'add accounts', 'as accounts']); // false

// dd($bool2);
        return view('frontend.index');
    }
}
