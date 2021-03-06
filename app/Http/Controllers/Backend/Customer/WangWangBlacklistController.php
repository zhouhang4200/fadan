<?php
namespace App\Http\Controllers\Backend\Customer;

use Auth;
use Illuminate\Http\Request;
use App\Models\WangWangBlacklist;
use App\Http\Controllers\Controller;

/**
 * 旺旺黑名单
 * 如果用户旺旺在列中则相关的用户订单不会进入集市，会直接失败
 * Class WangWangBlacklistController
 * @package App\Http\Controllers\Backend\Data
 */
class WangWangBlacklistController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $wangWang = $request->wang_wang;

        $wangWangBlacklist = WangWangBlacklist::filter(compact('wangWang'))->paginate(30);

        return view('backend.customer.wang-wang-blacklist.index', compact('wangWangBlacklist', 'wangWang'));
    }

    /**
     * 添加
     * @param Request $request
     */
    public function store(Request $request)
    {
        $wangWangBlacklist = WangWangBlacklist::firstOrNew(['wang_wang' => $request->wang_wang]);
        $wangWangBlacklist->wang_wang = $request->wang_wang;
        $wangWangBlacklist->admin_user_id = auth('admin')->user()->id;
        $wangWangBlacklist->save();

        return response()->ajax(1, '添加成功');
    }

    /**
     * 删除
     * @param Request $request
     */
    public function delete(Request $request)
    {
        WangWangBlacklist::where('id', $request->id)->delete();
        return response()->ajax(1, '删除成功');
    }
}
