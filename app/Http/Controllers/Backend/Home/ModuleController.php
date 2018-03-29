<?php

namespace App\Http\Controllers\Backend\Home;

use Illuminate\Http\Request;
use App\Models\NewModule;
use App\Http\Controllers\Controller;

class ModuleController extends Controller
{
	/**
	 * 模块
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$modules = NewModule::get();

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.home.module.list', [
                'modules' => $modules,
            ])->render());
    	}
    	return view('backend.home.module.index', compact('modules'));
    }
    /**
     * 添加
     * @param Request $request [description]
     */
    public function add(Request $request)
    {
    	$data['name'] = $request->data['name'];

    	NewModule::create($data);

    	return response()->ajax(1, '添加成功!');
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	$newModule = NewModule::find($request->id);

    	$newModule->name = $request->data['name'];

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
    	NewModule::destroy($request->id);

    	return response()->ajax(1, '删除成功!');
    }
}
