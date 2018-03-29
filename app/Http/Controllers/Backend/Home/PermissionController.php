<?php

namespace App\Http\Controllers\Backend\Home;

use Cache;
use Illuminate\Http\Request;
use App\Models\NewPermission;
use App\Models\NewModule;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
	 * 权限
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$permissions = NewPermission::with('newModule')->paginate(10);

        $modules = NewModule::get();

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.home.permission.list', [
                'permissions' => $permissions,
                'modules' => $modules,
            ])->render());
    	}
    	return view('backend.home.permission.index', compact('permissions', 'modules'));
    }

    /**
     * 添加
     * @param Request $request [description]
     */
    public function add(Request $request)
    {
    	$data['name'] = $request->data['name'];
    	$data['alias'] = $request->data['alias'];
    	$data['new_module_id'] = $request->data['new_module_id'];

    	NewPermission::create($data);

    	return response()->ajax(1, '添加成功!');
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	$newModule = NewPermission::find($request->id);

    	$newModule->name = $request->data['name'];
    	$newModule->alias = $request->data['alias'];
    	$newModule->new_module_id = $request->data['new_module_id'];
    	$newModule->save();

    	return response()->ajax(1, '修改成功!');
    }

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroy(Request $request)
    {
        $permission = NewPermission::find($request->id);
        // 删除角色下面的权限
        $permission->newRoles()->detach();
        // 删除用户-权限下面的权限
        $permission->newUsers()->detach();
        // 删除自己
        $permission->delete();
        
    	return response()->ajax(1, '删除成功!');
    }
}
