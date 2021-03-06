<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 权限 模块增删改查
 */
class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = Module::where('guard_name', 'web')->paginate(config('frontend.page'));

        return view('backend.rbac.module.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.rbac.module.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Module::rules(), Module::messages());

        $data['guard_name'] = 'web';
        $data['name'] = $request->name;
        $data['alias'] = $request->alias;
        $res = Module::create($data);
        
        if (! $res) {
            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('modules.index'))->with('succ', '添加成功!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = Module::find($id);
        return view('backend.rbac.module.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, Module::updateRules($id), Module::messages());
        $data['name'] = $request->name;
        $data['alias'] = $request->alias;
        $module = Module::find($id);
        $int = $module->update($data);
        if ($int > 0) {
            return redirect(route('modules.index'))->with('succ', '更新成功!');
        }

        return back()->withInput()->with('updateFail', '更新失败!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bool = Module::find($id)->delete();
        if ($bool) {
            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}
