<?php

namespace App\Http\Controllers\Backend\Steam;

use App\Http\Controllers\Frontend\Steam\Custom\Helper;
use App\Http\Controllers\Frontend\Steam\Services\SteamImportAccountAip;
use App\Models\SteamGoods;
use \Exception;
use App\Models\Goods;
use App\Repositories\backend\ServiceRepository;
use App\Repositories\backend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GoodsController
 * @package App\Http\Controllers\backend\Workbench
 */
class GoodsController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('name') and $request->name != '') {
                $name = "%" . $request->name . "%";
                $query->where('name', 'like', $name);
            }
        };
        $goods = SteamGoods::where($where)->paginate(config('backend.page'));
        return view('backend.steam.goods.index', compact('goods'));

    }

    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('backend.steam.goods.create');
    }

    /**
     * 创建
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $goodsData = $request->data;
        $name = SteamGoods::where('name', $goodsData['name'])->first();
        $subid = SteamGoods::where('subid', $goodsData['subid'])->first();

        if ($name) {
            return response()->ajax('0', '版本号已存在');
        }
        if ($subid) {
            return response()->ajax('0', 'subid已存在');
        }

        try {
            unset($goodsData['file']);
            $goodsData['user_id'] = Auth::user()->id;
            SteamGoods::create($goodsData);
            return response()->ajax('1', '添加成功');
        } catch (Exception $exception) {
            return response()->ajax('0', $exception->getMessage());
        }
    }

    /**
     * 修改视图
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $goods = SteamGoods::find($id);

        return view('backend.steam.goods.edit', compact('goods'));
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
            return response()->ajax('0', '修改失败');
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return mixed
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
     * 商品
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function examineGoods(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('name') and $request->name != '') {
                $name = "%" . $request->name . "%";
                $query->where('name', 'like', $name);
            }
        };
        $goods = SteamGoods::where($where)->where('is_examine', true)->orderBy('sortord')->paginate(config('backend.page'));
        return view('backend.steam.goods.examine-goods', compact('goods'));
    }

    /**
     * Ajax修改属性
     * @param Request $request
     * @return array
     */
    function isSomething(Request $request)
    {
        $attr = $request->attr;
        $cdkeyLibrary = SteamGoods::find($request->id);
        if ($attr == 'is_examine') {
            if ($request->value) {
                $value = $request->value;
                if ($value == 1) {
                    $this->addGameName($request->gameName);
                }
            } else {
                $this->addGameName($request->gameName);
                $value = $cdkeyLibrary->is_examine = 1;
            }
        } else {
            $value = $cdkeyLibrary->$attr ? 0 : 1;
        }
        $cdkeyLibrary->$attr = $value;

        if ($cdkeyLibrary->save()) {
            return response()->ajax('1', '修改成功');
        } else {
            return response()->ajax('0', '修改失败');
        };
    }

    public function editSomething(Request $request)
    {
        $attr = $request->attr;
        if ($attr == 'subid') {
            $subid = SteamGoods::where('subid', $request->value)->first();
            if ($subid) {
                return response()->ajax('0', '修改失败:subid已经存在');
            }
        }

        if ($attr == 'game_name') {
            $this->addGameName($request->value);
        }

        $goods = SteamGoods::find($request->id);
        $goods->$attr = $request->value;
        if ($goods->save()) {
            return response()->ajax('1', '修改成功');
        } else {
            return response()->ajax('0', '修改失败');
        };
    }


    /**
     * 游戏模板
     * @return $this
     */
    public function getGameNameList()
    {
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->getGameNameList();
        if (isset($result->Data) && $result->Data == null) {
            $data = null;
        } else {
            $data = isset($result->data) ? $result->data : null;
        }
        return view('backend.steam.goods.game-list')->with([
            'data' => $data,
        ]);

    }

    /**
     * 创建游戏
     * @param Request $request
     * @return mixed
     */
    public function insertGameName(Request $request)
    {
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->insertGameName($request->game_name, Auth::user()->name);
        if ($result->Code != 1) {
            return response()->ajax('0', $result->Message);
        }
        return response()->ajax('1', '添加成功');
    }

    public function addGameName($game_name)
    {
        try {
            $steamImportAccountAip = new SteamImportAccountAip();
            $steamImportAccountAip->insertGameName($game_name, Auth::user()->name);
        } catch (\Exception $e) {
            Helper::log('add-game-name', [$e->getMessage(), ['游戏名称' => $game_name]]);
        }
    }

    /**
     * 点击图片 ajax 上传
     * @param  Illuminate\Http\Request
     * @return json
     */
    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = public_path("/resources/goods/".date('Ymd')."/");
            $imagePath = $this->uploadImage($file, $path);

            return response()->json(['code' => 1, 'path' => $imagePath]);
        }
    }

    /**
     * 图片上传
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param  $path string
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path)
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension && ! in_array(strtolower($extension), static::$extensions)) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (! $file->isValid()) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (!file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $randNum = rand(1, 100000000) . rand(1, 100000000);

        $fileName = time().substr($randNum, 0, 6).'.'.$extension;

        $path = $file->move($path, $fileName);

        $path = strstr($path, '/resources');

        return str_replace('\\', '/', $path);
    }

}
