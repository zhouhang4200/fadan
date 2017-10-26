<?php

namespace App\Http\Controllers\Frontend;

use Auth, Weight;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Models\RealNameIdent;
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
        $user = Auth::user();

        $loginHistoryTime = LoginHistory::where('user_id', $user->id)->latest('created_at')->value('created_at');

        $masterId = $user->parent_id == 0 ? $user->id : $user->parent_id;

        $ident = RealNameIdent::where('user_id', $masterId)->first();

        // $aa = \App\Extensions\Weight\Algorithm\OrderSix::compute([1, 29]);

        // $bb = \App\Extensions\Weight\Algorithm\OrderSuccess::compute([1, 29]);

        // $dd = \App\Extensions\Weight\Algorithm\OrderUseTime::compute([1, 29]);
        // $cc = \App\Http\Controllers\Frontend\MarketWeightData::marketUserDatas([1, 29]);

        // $a = (new \App\Extensions\Weight\Weight)->run([1, 29]);

        // dd($a);
        /**
        $data = '<?xml version="1.0" encoding="utf-8"?><Orders><Order><OrderNo>1130471945</OrderNo><OrderStatus>%e6%9c%aa%e5%a4%84%e7%90%86</OrderStatus><BuyTime>2017-10-23+23%3a59%3a51</BuyTime><BuyNum>1</BuyNum><ProductId>1087715</ProductId><ProductPrice>55.0000</ProductPrice><ProductName>%e7%8e%8b%e8%80%85%e8%8d%a3%e8%80%80+68%e5%85%83</ProductName><ProductType>%e5%9c%a8%e7%ba%bf%e7%9b%b4%e5%82%a8</ProductType><TemplateId>9e5b7ccd-521a-4f46-a688-7aa587593b17</TemplateId><ChargeAccount>lzzzzz88</ChargeAccount><ChargePassword>qqqqqqqq</ChargePassword><ChargeGame>%e7%8e%8b%e8%80%85%e8%8d%a3%e8%80%80</ChargeGame><ChargeRegion>%e5%be%ae%e4%bf%a19%e5%8c%ba-%e5%a5%b3%e5%b8%9d%e5%a8%81%e4%b8%a5</ChargeRegion><ChargeServer></ChargeServer><ChargeType></ChargeType><JSitid>105279</JSitid><GSitid>90347</GSitid><BuyerIp>123.134.14.199</BuyerIp><OrderFrom>5</OrderFrom><RoleName>%7e%7e%e4%ba%ae%e5%8f%94%e3%80%81</RoleName><RemainingNumber>0</RemainingNumber><ContactType>9869933%40qq.com</ContactType><ContactQQ>liangne0921</ContactQQ><UseAccount>%40qq.com%2cw*****4%2c276133%7c</UseAccount><CustomerOrderNo>73212271213328013</CustomerOrderNo></Order></Orders>';
 
        $a =  ForeignOrderFactory::choose('kamen')->outputOrder($data);
        
        dd($a);
        */
        //  return Weight::run([1]);
    	// $user = Auth::user();

    	// $role = Role::where('name', 'manager')->first();

    	// $role2 = Role::where('name', 'writer')->first();

    	// $permission = Permission::where('name', 'add accounts')->first();

    	// $permission2 = Permission::where('name', 'edit accounts')->first();

    	// $permission3 = Permission::where('name', 'delete accounts')->first();
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
        return view('frontend.index', compact('user', 'loginHistoryTime', 'ident'));
    }
}
