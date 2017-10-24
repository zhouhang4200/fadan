<?php

namespace App\Http\Controllers\Frontend;

use Auth, Weight;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

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
        $data = '<?xml version="1.0" encoding="utf-8"?><Orders><Order><OrderNo>1080711898</OrderNo><OrderStatus>%e6%9c%aa%e5%a4%84%e7%90%86</OrderStatus><BuyTime>2017-8-26+16%3a49%3a15</BuyTime><BuyNum>1</BuyNum><ProductId>1087715</ProductId><ProductPrice>59.5000</ProductPrice><ProductName>%e7%8e%8b%e8%80%85%e8%8d%a3%e8%80%80+68%e5%85%83</ProductName><ProductType>%e5%9c%a8%e7%ba%bf%e7%9b%b4%e5%82%a8</ProductType><TemplateId>9e5b7ccd-521a-4f46-a688-7aa587593b17</TemplateId><ChargeAccount>%e5%be%ae%e4%bf%a1</ChargeAccount><ChargePassword>%e5%be%ae%e4%bf%a1</ChargePassword><ChargeGame>%e7%8e%8b%e8%80%85%e8%8d%a3%e8%80%80</ChargeGame><ChargeRegion>%e8%af%b7%e9%80%89%e6%8b%a9%e5%8c%ba%e6%9c%8d</ChargeRegion><ChargeServer></ChargeServer><ChargeType></ChargeType><JSitid>105279</JSitid><GSitid>90347</GSitid><BuyerIp>59.172.249.162</BuyerIp><OrderFrom>5</OrderFrom><RoleName>%e3%81%ae%e5%85%ab%e8%8d%92%e7%ac%ac%e4%b8%80%e7%be%8e</RoleName><RemainingNumber>435</RemainingNumber><ContactType>18062496888</ContactType><ContactQQ>qq1205648711</ContactQQ><UseAccount>%40qq.com%2cw*****4%2c276133%7c</UseAccount><CustomerOrderNo>51621368077269676</CustomerOrderNo></Order></Orders>';
// dd(ForeignOrderFactory::choose('kamen'));
        $a =  ForeignOrderFactory::choose('kamen')->outputOrder($data);
        
        dd($a);

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
