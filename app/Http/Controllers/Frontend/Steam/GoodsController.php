<?php

namespace App\Http\Controllers\Frontend\Steam;

use \Exception;
use App\Models\SteamGoods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GoodsController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class GoodsController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];
    /**
     *
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
            $query->where('is_show', true);
            $query->where('user_id', Auth::user()->id);
        };
        $goods = SteamGoods::where($where)->paginate(config('frontend.page'));
        return view('frontend.steam.goods.index', compact('goods'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('frontend.steam.goods.create');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $goodsData = $request->data;
        $goods = SteamGoods::where('name', $goodsData['name'])->first();
        if ($goods) {
            return response()->ajax(0, '版本号不能重复');
        }
        try {
            unset($goodsData['file']);
            $goodsData['user_id'] = Auth::user()->id;
            SteamGoods::create($goodsData);
            return response()->ajax('1', '添加成功');
        } catch (Exception $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * @param GameRepository $gameRepository
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $goods = SteamGoods::find($id);

        return view('frontend.steam.goods.edit', compact('goods'));
    }

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
     * 多条件查询
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
            $query->where('is_show', true);
        };
        $goods = SteamGoods::where($where)->where('is_examine', true)->paginate(config('frontend.page'));
        return view('frontend.steam.goods.examine-goods', compact('goods'));
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
