<?php

namespace App\Http\Controllers\Frontend\Steam;

use App\Http\Controllers\Frontend\Steam\Excel\CdkeyLlibrarytExport;
use App\Models\SteamCdkey;
use App\Models\SteamCdkeyLibrary;
use Carbon\Carbon;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * 平台卡类
 * Class CdkeyController
 * @package App\Http\Controllers\Frontend\Cdkey
 */
class CdkeyLibraryController extends Controller
{

    /**
     * 平台卡列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, CdkeyLlibrarytExport $excel)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('cdk') and $request->cdk != '') {
                $query->where('cdk', $request->cdk);
            }
            $query->where('cdkey_id', $request->id);
        };

        $cdkey = SteamCdkey::with('goodses')->find($request->id);
        $cdkeyLibraries = SteamCdkeyLibrary::where($where)->orderBy('id', 'desc')->paginate(config('frontend.page'));
        if ($request->has('export')) {
            $cdkeyLibraries = SteamCdkeyLibrary::where($where)->orderBy('id', 'desc')->get();
            $excel->export($cdkeyLibraries, $cdkey);
        }

        foreach ($cdkeyLibraries as $v) {
            $effectiveTime = Carbon::parse($v->effective_time);
            $effectiveTimeInt = (new Carbon())->diffInSeconds($effectiveTime, false);
            if ($effectiveTimeInt < 0) { //判断数据库到期时间跟当前时间比较
                if ($v->status == 1) {
                    $v->status = 3; // 3已过期
                    $v->save();
                }
            }
        }
        return view('frontend.steam.cdkey.cdkeylibrary.index', compact('cdkeyLibraries', 'cdkey'));
    }

    public function search(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('cdk') and $request->cdk != '') {
                $query->where('cdk', $request->cdk);
            }
        };

        if ($request->has('cdk') and $request->cdk == '') {
            return redirect(route('frontend.steam.cdkey.index'));
        }

        $cdkey_id = SteamCdkeyLibrary::where('cdk', $request->cdk)->value('cdkey_id');
        if (!$cdkey_id) {
            return redirect(route('frontend.steam.cdkey.index'));
        }

        $cdkey = SteamCdkey::with('goodses')->find($cdkey_id);
        $cdkeyLibraries = SteamCdkeyLibrary::where($where)->orderBy('id', 'desc')->paginate(config('frontend.page'));
        return view('frontend.steam.cdkey.cdkeylibrary.search', compact('cdkeyLibraries', 'cdkey'));

    }

    /**
     * Ajax修改属性
     * @param Request $request
     * @return array
     */
    function isSomething(Request $request)
    {
        $attr = $request->attr;
        $cdkeyLibrary = SteamCdkeyLibrary::find($request->id);
        $value = $cdkeyLibrary->$attr ? 0 : 1;
        $cdkeyLibrary->$attr = $value;
        $value == 1 ? $cdkeyLibrary->status = 2 : $cdkeyLibrary->status = 1;

        if ($cdkeyLibrary->save()) {
            return response()->ajax('1', '修改成功');
        } else {
            return response()->ajax('0', '修改失败');
        };
    }

}
