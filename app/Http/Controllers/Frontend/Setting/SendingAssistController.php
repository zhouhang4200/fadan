<?php

namespace App\Http\Controllers\Frontend\Setting;

use Auth;
use Illuminate\Http\Request;
use App\Models\OrderTemplate;
use App\Http\Controllers\Controller;

class SendingAssistController extends Controller
{
	/**
	 * 要求代练模板
	 * @return [type] [description]
	 */
    public function require(Request $request)
    {
    	$orderTemplates = OrderTemplate::where('user_id', Auth::user()->getPrimaryUserId())->paginate(2);

    	if ($request->ajax()) {
            return response()->json(view()->make('frontend.setting.sending-assist.require-form', [
                'orderTemplates' => $orderTemplates,
            ])->render());
        }

    	return view('frontend.setting.sending-assist.require', compact('orderTemplates'));
    }

    /**
     * 要求代练模板-新增功能
     * @return [type] [description]
     */
    public function requireCreate()
    {
    	return view('frontend.setting.sending-assist.require-create');
    }

    /**
     * 模板添加
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireStore(Request $request)
    {
    	// 检查输入是否为空
    	$res = $this->validate($request, OrderTemplate::rules(), OrderTemplate::messages());
    	// 数据
    	$datas['name'] = $request->name;
    	$datas['content'] = $request->content;
    	$datas['user_id'] = Auth::user()->getPrimaryUserId();

    	OrderTemplate::create($datas);

    	return response()->ajax(1, '添加成功!');
    }

    /**
     * 设置默认模板
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireSet(Request $request)
    {
    	// 获取当前的模型
    	$orderTemplate = OrderTemplate::find($request->id);

    	if ($orderTemplate->status == 0) {
	    	// 设置当前的值为1
	    	$OrderTemplate = OrderTemplate::where('id', $request->id)->update(['status' => 1]);
    	} elseif ($orderTemplate->status == 1) {
    		// 设置当前的值为1
	    	$OrderTemplate = OrderTemplate::where('id', $request->id)->update(['status' => 0]);
    	}
    	// 将其他值设置为0
    	OrderTemplate::where('id', '!=', $request->id)
    		->where('user_id', Auth::user()->getPrimaryUserId())
    		->update(['status' => 0]);

    	return response()->ajax(1, '设置成功!');
    }

    /**
     * 下单要求模板-编辑
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function requireEdit($id)
    {
    	// 获取当前模型
    	$orderTemplate = OrderTemplate::find($id);

    	return view('frontend.setting.sending-assist.require-edit', compact('orderTemplate'));
    }

    /**
     * 下单要求模板 - 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireUpdate(Request $request)
    {
    	// 获取当前模型
    	$orderTemplate = OrderTemplate::find($request->id);
    	$orderTemplate->name = $request->name;
    	$orderTemplate->content = $request->content;
    	$orderTemplate->save();

    	return response()->ajax(1, '修改成功!');
    }

    /**
     * 发单要去模板-删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireDestroy(Request $request)
    {
    	OrderTemplate::destroy($request->id);

    	return response()->ajax(1, '删除成功!');
    }
}
