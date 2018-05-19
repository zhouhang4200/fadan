<?php

namespace App\Http\Controllers\Frontend\Account;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HatchetManBlacklist;

class HatchetManBlacklistController extends Controller
{
 	public function index(Request $request)
 	{
 		$hatchetManName = $request->hatchet_man_name;
 		$hatchetManPhone = $request->hatchet_man_phone;
 		$hatchetManQq = $request->hatchet_man_qq;

 		$hatchetMans = HatchetManBlacklist::where('user_id', Auth::user()->getPrimaryUserId())->get();
 		 // 筛选
    	$filters = compact('hatchetManName', 'hatchetManPhone', 'hatchetManQq');

 		$hatchetManBlacklists = HatchetManBlacklist::where('user_id', Auth::user()->getPrimaryUserId())
 			->filter($filters)
 			->paginate(10);

        // 删除的时候页面不刷新
        if ($request->ajax()) {
            return response()->json(view()->make('frontend.v1.user.hatchet-man-blacklist.list', [
                'hatchetManBlacklists' => $hatchetManBlacklists,
            ])->render());
        }

    	return view('frontend.v1.user.hatchet-man-blacklist.index', compact('hatchetManName', 'hatchetManPhone', 'hatchetManQq', 'hatchetManBlacklists', 'hatchetMans'));
 	}

 	public function create(Request $request)
 	{
 		return view('frontend.v1.user.hatchet-man-blacklist.create');
 	}

 	public function store(Request $request)
 	{
 		if (! isset($request->data['hatchet_man_name']) || ! isset($request->data['hatchet_man_qq']) || ! isset($request->data['hatchet_man_phone'])) {
 			return response()->ajax(0, '带*为必填内容');
 		}
 		$data['user_id'] = Auth::user()->getPrimaryUserId();
 		$data['hatchet_man_name'] = $request->data['hatchet_man_name'];
 		$data['hatchet_man_phone'] = $request->data['hatchet_man_phone'];
 		$data['hatchet_man_qq'] = $request->data['hatchet_man_qq'];
 		$data['content'] = $request->data['content'] ?? '';

 		$res = HatchetManBlacklist::create($data);

 		if (! $res) {
 			return response()->ajax(0, '添加失败');
 		}
 		return response()->ajax(1, '添加成功');
 	}

 	public function edit(Request $request)
 	{
 		$hatchetManBlacklist = HatchetManBlacklist::find($request->id);

 		return view('frontend.v1.user.hatchet-man-blacklist.edit', compact('hatchetManBlacklist'));
 	}

 	public function update(Request $request)
 	{
 		if (! isset($request->data['hatchet_man_name']) || ! isset($request->data['hatchet_man_qq']) || ! isset($request->data['hatchet_man_phone'])) {
 			return response()->ajax(0, '带*为必填内容');
 		}
 		
 		if (! isset($request->id)) {
 			return response()->ajax(0, '该记录未找到');
 		}
 		$hatchetManBlacklist = HatchetManBlacklist::find($request->id);

 		$hatchetManBlacklist->user_id = Auth::user()->getPrimaryUserId();
 		$hatchetManBlacklist->hatchet_man_name = $request->data['hatchet_man_name'];
 		$hatchetManBlacklist->hatchet_man_phone = $request->data['hatchet_man_phone'];
 		$hatchetManBlacklist->hatchet_man_qq = $request->data['hatchet_man_qq'];
 		$hatchetManBlacklist->content = $request->data['content'] ?? '';

 		$res = $hatchetManBlacklist->save();

 		if (! $res) {
 			return response()->ajax(0, '编辑失败');
 		}
 		return response()->ajax(1, '编辑成功');
 	}

 	public function delete(Request $request)
 	{
 		if (! isset($request->id)) {
 			return response()->ajax(0, '该记录未找到');
 		}
 		$del = HatchetManBlacklist::destroy($request->id);

 		if (! $del) {
 			return response()->ajax(0, '删除失败');
 		}

 		return response()->ajax(1, '删除成功');
 	}
}
