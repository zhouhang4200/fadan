<?php

namespace App\Http\Controllers\Frontend\Steam;

use App\Models\SteamCdkey;
use App\Models\SteamCdkeyLibrary;
use App\Models\SteamGoods;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 平台卡类
 * Class CdkeyController
 * @package App\Http\Controllers\Frontend\Cdkey
 */
class CdkeyController extends Controller
{

    /**
     * 平台卡列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('cdk') and $request->cdk != '') {
                $name = "%" . $request->cdk . "%";
                $goods_ids = DB::table('goods')->where('name', 'like', $name)->pluck('id');
                $query->whereIn('goods_id', $goods_ids);
            }
            $query->where('user_id', Auth::user()->id);
        };
        $cdkies = SteamCdkey::with(['goodses' => function ($query) {
            $query->where('is_show', 1);
        }
        ])->where($where)->orderBy('id', 'desc')->paginate(config('frontend.page'));
        return view('frontend.steam.cdkey.index', compact('cdkies'));
    }

    /**
     * 生成平台卡
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $goodsData = $request->data;
        try {

            $yearLater = Carbon::now()->addYears(1)->toDateTimeString();
            $data['user_id'] = Auth::user()->id;
            $data['goods_id'] = $goodsData['goods_id'];
            $data['number'] = $goodsData['number'];
            $data['end_time'] = empty($goodsData['effective_time']) ? $yearLater : $goodsData['effective_time'];
            $ckdey = SteamCdkey::create($data);
            for ($i = 1; $i <= $goodsData['number']; $i++) {
                $librarydata['cdkey_id'] = $ckdey->id;
                $librarydata['account'] = strtoupper(Str::random($length = 12) . $i);
                $librarydata['password'] = rand(1, 1000000000);
                $librarydata['effective_time'] = empty($goodsData['effective_time']) ? $yearLater : $goodsData['effective_time'];
                $librarydata['user_id'] = Auth::user()->id;
                $librarydata['cdk'] = strtoupper(substr(md5($librarydata['account'] . $librarydata['password']), 8, 16));
                SteamCdkeyLibrary::create($librarydata);
            }
            return response()->ajax('1', '添加成功', ['cdkey_id' => $ckdey->id]);
        } catch (Exception $exception) {
            return response()->ajax('0', $exception->getMessage());
        }
    }

    /**
     * 编辑
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $goods = SteamGoods::find($id);

        return view('frontend.steam.goods.edit', compact('goods'));
    }

    /**
     * 更新
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        try {
            $data = $request->data;
            unset($data['file']);
            $goods = SteamGoods::find($data['id']);
            $goods->update($data);
            return response()->ajax('1', '修改成功');
        } catch (Exception $exception) {
            return response()->ajax(0, '修改失败');
        }
    }

    /**
     * 显示详情
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return SteamGoods::find($id);
    }

    /**
     * 删除
     */
    public function destroy(Request $request)
    {
        $int = SteamGoods::destroy($request->id);
        if ($int) {
            return response()->ajax(['code' => '1', 'message' => '删除成功']);
        } else {
            return response()->ajax(['code' => '2', 'message' => '删除失败']);
        }
    }

    /**
     * 备注
     * @param Request $request
     * @return mixed
     */
    public function remarks(Request $request)
    {

        $goods = SteamCdkey::find($request->id);
        $goods->remarks = $request->remarks;
        if ($goods->save()) {
            return response()->ajax(['code' => '1', 'message' => '修改成功']);
        } else {
            return response()->ajax(['code' => '2', 'message' => '修改失败']);
        };
    }
}
