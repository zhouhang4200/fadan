<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Repositories\Frontend\GameRepository;
use Auth;
use Illuminate\Http\Request;
use App\Models\GameLevelingRequirementsTemplate;
use App\Models\OrderAutoMarkup;
use App\Http\Controllers\Controller;

class SendingAssistController extends Controller
{
	/**
	 * 要求代练模板
	 * @return [type] [description]
	 */
    public function require(Request $request)
    {
    	$orderTemplates = GameLevelingRequirementsTemplate::where('user_id', Auth::user()->getPrimaryUserId())
            ->with('game')
            ->paginate(10);

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
    public function requireCreate(GameRepository $gameRepository)
    {
        $game = $gameRepository->availableByServiceId(4);
    	return view('frontend.setting.sending-assist.require-create', compact('game'));
    }

    /**
     * 模板添加
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireStore(Request $request)
    {
    	// 检查输入是否为空
    	$res = $this->validate($request, GameLevelingRequirementsTemplate::rules(), GameLevelingRequirementsTemplate::messages());
    	// 数据
    	$datas['name'] = $request->name;
    	$datas['game_id'] = $request->game_id;
    	$datas['content'] = $request->content;
    	$datas['user_id'] = Auth::user()->getPrimaryUserId();

        GameLevelingRequirementsTemplate::create($datas);

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
    	$orderTemplate = GameLevelingRequirementsTemplate::find($request->id);

    	if ($orderTemplate->status == 0) {
	    	// 设置当前的值为1
	    	$OrderTemplate = GameLevelingRequirementsTemplate::where('id', $request->id)->update(['status' => 1]);
    	} elseif ($orderTemplate->status == 1) {
    		// 设置当前的值为0
	    	$OrderTemplate = GameLevelingRequirementsTemplate::where('id', $request->id)->update(['status' => 0]);
    	}
    	// 将其他值设置为0
        GameLevelingRequirementsTemplate::where('id', '!=', $request->id)
    		->where('user_id', Auth::user()->getPrimaryUserId())
            ->where('game_id', $orderTemplate->game_id)
    		->update(['status' => 0]);

    	return response()->ajax(1, '设置成功!');
    }

    /**
     * 下单要求模板-编辑
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function requireEdit($id, GameRepository $gameRepository)
    {
    	// 获取当前模型
    	$orderTemplate = GameLevelingRequirementsTemplate::find($id);
        $game = $gameRepository->availableByServiceId(4);

    	return view('frontend.setting.sending-assist.require-edit', compact('orderTemplate', 'game'));
    }

    /**
     * 下单要求模板 - 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function requireUpdate(Request $request)
    {
    	// 获取当前模型
    	$orderTemplate = GameLevelingRequirementsTemplate::find($request->id);
    	$orderTemplate->name = $request->name;
    	$orderTemplate->game_id = $request->game_id;
    	$orderTemplate->content = $request->content;
        $orderTemplate->status = 0;
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
        GameLevelingRequirementsTemplate::destroy($request->id);

    	return response()->ajax(1, '删除成功!');
    }

    /**
     * 自动加价列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoMarkup(Request $request)
    {
        $orderAutoMarkups = OrderAutoMarkup::where('user_id', Auth::user()->getPrimaryUserId())
            ->oldest('markup_amount')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json(view()->make('frontend.setting.sending-assist.auto-markup-list', [
                'orderAutoMarkups' => $orderAutoMarkups,
            ])->render());
        }

        return view('frontend.setting.sending-assist.auto-markup', compact('orderAutoMarkups'));
    }

    /**
     * 自动加价添加列表
     * @return [type] [description]
     */
    public function autoMarkupCreate()
    {
        return view('frontend.setting.sending-assist.auto-markup-create');
    }

    /**
     * 自动加价保存
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoMarkupStore(Request $request)
    {
        // 数据
        $datas['user_id'] = Auth::user()->getPrimaryUserId();
        // 数据
        $requestMarkupAmount = is_numeric($request->data['markup_amount']) ? round($request->data['markup_amount'], 2) : 0;
        // 如果填的值不合法则
        if (! $requestMarkupAmount) {
            return response()->ajax(0, '发单价和加价频率加价次数必须大于0');
        }
        // 查看发单价是否有重复
        $sameMarkupAmount = OrderAutoMarkup::where('user_id', Auth::user()->getPrimaryUserId())
            ->where('markup_amount', $requestMarkupAmount)
            ->count();

        if ($sameMarkupAmount > 0) {
            return response()->ajax(0, '发单价已存在');
        }
        // 数据
        $datas['markup_amount'] = $requestMarkupAmount;
        $datas['markup_time'] = is_numeric($request->data['hours']) && is_numeric($request->data['minutes']) ?
                                intval(bcadd(bcmul(60, $request->data['hours']), $request->data['minutes'])) : 0;
        $datas['markup_type'] = $request->data['markup_type'];
        $datas['markup_money'] = is_numeric($request->data['markup_money']) ? round($request->data['markup_money'], 2) : 0;
        $datas['markup_frequency'] = is_numeric($request->data['markup_frequency']) ? intval($request->data['markup_frequency']) : 0;
        $datas['markup_number'] = is_numeric($request->data['markup_number']) ? intval($request->data['markup_number']) : 0;

        // 保存
        $res = OrderAutoMarkup::create($datas);

        if (! $res) {
            return response()->ajax(0, '添加失败!');
        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 自动加价配置删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoMarkupDestroy(Request $request)
    {
        OrderAutoMarkup::destroy($request->id);

        return response()->ajax(1, '删除成功!');
    }

    /**
     * 自动加价配置编辑
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoMarkupEdit(Request $request)
    {
        $orderAutoMarkup = OrderAutoMarkup::find($request->id);

        return view('frontend.setting.sending-assist.auto-markup-edit', compact('orderAutoMarkup'));
    }

    /**
     * 自动加价配置编辑
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function autoMarkupUpdate(Request $request)
    {
        $orderAutoMarkup = OrderAutoMarkup::find($request->data['id']);
        // 数据
        $requestMarkupAmount = is_numeric($request->data['markup_amount']) ? round($request->data['markup_amount'], 2) : 0;
        // 如果填的值不合法则
        if (! $requestMarkupAmount) {
            return response()->ajax(0, '发单价和加价频率加价次数必须大于0');
        }
        // 查看发单价是否有重复
        $sameMarkupAmount = OrderAutoMarkup::where('user_id', $orderAutoMarkup->user_id)
            ->where('markup_amount', $requestMarkupAmount)
            ->count();

        if ($sameMarkupAmount > 0 && $orderAutoMarkup->markup_amount != $requestMarkupAmount) {
            return response()->ajax(0, '发单价已存在');
        }
        // 数据
        $orderAutoMarkup->markup_amount = $requestMarkupAmount;
        $orderAutoMarkup->markup_time = is_numeric($request->data['hours']) && is_numeric($request->data['minutes']) ?
                                intval(bcadd(bcmul(60, $request->data['hours']), $request->data['minutes'])) : 0;
        $orderAutoMarkup->markup_type = $request->data['markup_type'];
        $orderAutoMarkup->markup_money = is_numeric($request->data['markup_money']) ? round($request->data['markup_money'], 2) : 0;
        $orderAutoMarkup->markup_frequency = is_numeric($request->data['markup_frequency']) ? intval($request->data['markup_frequency']) : 0;
        $orderAutoMarkup->markup_number = is_numeric($request->data['markup_number']) ? intval($request->data['markup_number']) : 0;
        // 保存
        $res = $orderAutoMarkup->save();

        if (! $res) {
            return response()->ajax(0, '更新失败!');
        }
        return response()->ajax(1, '更新成功!');
    }
}
