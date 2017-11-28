<?php

namespace App\Http\Controllers\Backend\Punish;

use App\Models\PunishType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PunishTypeController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $punishTypes = PunishType::get();

        return view('backend.punish.type.index', compact('punishTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.punish.type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $this->validate($request, PunishType::rules(), PunishType::messages());

        $res = PunishType::create($data);
        
        if (! $res) {

            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('punish-types.index'))->with('succ', '添加成功!');
    }

    public function edit($id)
    {
        $punishType = PunishType::find($id);

        return view('backend.punish.type.edit', compact('punishType'));
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
        $this->validate($request, PunishType::updateRules($id), PunishType::messages());

        $punishType = PunishType::find($id);

        $int = $punishType->update($request->all());

        if ($int > 0) {
            return redirect(route('punish-types.index'))->with('succ', '更新成功!');
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
        $bool = PunishType::find($id)->delete();

        if ($bool) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }

}
