<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Exceptions\CustomException;
use App\Extensions\Asset\Recharge;
use App\Models\Goods;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\ReceivingControlRepository;

/**
 * Class ReceivingControlController
 * @package App\Http\Controllers\Frontend\Setting
 */
class ReceivingControlController extends Controller
{
    /**
     * @var ReceivingControlRepository
     */
    protected $receivingControlRepository;

    /**
     * ReceivingControlController constructor.
     * @param ReceivingControlRepository $receivingControlRepository
     */
    public function __construct(ReceivingControlRepository $receivingControlRepository)
    {
        $this->receivingControlRepository = $receivingControlRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $gameId = $request->game_id;
        $serviceId = $request->service_id;
        $otherUserId = $request->other_user_id;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $goods = Goods::where('user_id', Auth::user()->getPrimaryUserId())->pluck('name', 'id');

        // 用户白名单
        $userWitheList = $this->receivingControlRepository->userList(1);
        // 用户黑名单
        $userBlacklist = $this->receivingControlRepository->userList(2);

        // 获取用户的接单权限设置
        $receivingControl = isset(Auth::user()->getUserSetting()['receiving_control']) ?
            Auth::user()->getUserSetting()['receiving_control'] : 0;

        return view('frontend.setting.receiving-control.index', compact('services', 'games', 'goods',
            'gameId', 'serviceId', 'otherUserId', 'userWitheList', 'userBlacklist', 'receivingControl'));
    }

    /**
     * 设置控制模式
     * @param Request $request
     */
    public function controlMode(Request $request)
    {
        if (in_array($request->model, config('user.setting.receiving_control'))) {
            // 写入或更新设置数据
            UserSetting::updateOrCreate(['user_id' => Auth::user()->getPrimaryUserId(), 'option' => 'receiving_control'], [
                'option' => 'receiving_control',
                'value' => $request->model,
                'user_id' => Auth::user()->id,
            ]);
            // 刷新用户设置缓存
            refreshUserSetting();
            return response()->ajax(1, '设置成功');
        }
        return response()->ajax(0, '非法参数');

    }

    /**
     * 按用户类型添加
     * @param Request $request
     */
    public function addUser(Request $request)
    {
        if (!in_array($request->data['type'], [1 , 2]) || !is_numeric($request->data['other_user_id'])) {
            return response()->ajax(0, '添加失败');
        }
        try {
            $this->receivingControlRepository->addUser($request->data['type'], $request->data['other_user_id'], $request->data['remark']);
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 按商品添加
     * @param Request $request
     */
    public function addCategory(Request $request)
    {
        if (!in_array($request->data['type'], [1 , 2]) || !is_numeric($request->data['other_user_id'])) {
            return response()->ajax(0, '添加失败');
        }
        try {
            $this->receivingControlRepository->addCategory($request->data['type'],
                $request->data['service_id'],
                $request->data['game_id'],
                $request->data['other_user_id'],
                $request->data['remark']);
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }


    /**
     * 按商品添加
     * @param Request $request
     * @return mixed
     */
    public function addGoods(Request $request)
    {
        if (!in_array($request->data['type'], [1 , 2]) || !is_numeric($request->data['other_user_id'])) {
            return response()->ajax(0, '添加失败');
        }
        try {
            $this->receivingControlRepository->addGoods($request->data['type'],
                $request->data['other_user_id'],
                $request->data['goods_id'],
                $request->data['remark']);
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getControlUser(Request $request)
    {
        $type = $request->input('type', 1);
        $otherUserId = $request->input('other_user_id');

        $controlUserList = $this->receivingControlRepository->userList($type, $otherUserId, 10);

        if ($request->ajax()) {
            if (!in_array($type, [1 , 2])) {
                return response()->ajax(0, '添加失败');
            }
            return response()->json(\View::make('frontend.setting.receiving-control.control-user-list', [
                'controlUserList' => $controlUserList,
                'type' => $type,
                'otherUserId' => $otherUserId
            ])->render());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getControlCategory(Request $request, ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $type = $request->input('type', 1);
        $gameId = $request->input('game_id');
        $serviceId = $request->input('service_id');
        $otherUserId = $request->input('other_user_id');

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();

        // 获取数据
        $controlCategoryList = $this->receivingControlRepository->categoryList($type, $otherUserId, $serviceId, $gameId);

        if ($request->ajax()) {
            if (!in_array($type, [1 , 2])) {
                return response()->ajax(0, '不存在的类型');
            }
            return response()->json(\View::make('frontend.setting.receiving-control.control-category-list', [
                'controlCategoryList' => $controlCategoryList,
                'type' => $type,
                'gameId' => $gameId,
                'serviceId' => $serviceId,
                'otherUserId' => $otherUserId,
                'services' => $services,
                'games' => $games,
            ])->render());
        }
    }

    /**
     * @param Request $request
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return JsonResponse
     */
    public function getControlGoods(Request $request, ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $type = $request->input('type', 1);
        $gameId = $request->input('game_id');
        $serviceId = $request->input('service_id');
        $otherUserId = $request->input('other_user_id');

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();

        // 获取数据
        $controlCategoryList = $this->receivingControlRepository->goodsList($type, $otherUserId, $serviceId, $gameId);

        if ($request->ajax()) {
            if (!in_array($type, [1 , 2])) {
                return response()->ajax(0, '不存在的类型');
            }
            return response()->json(\View::make('frontend.setting.receiving-control.control-goods-list', [
                'controlCategoryList' => $controlCategoryList,
                'type' => $type,
                'gameId' => $gameId,
                'serviceId' => $serviceId,
                'otherUserId' => $otherUserId,
                'services' => $services,
                'games' => Goods::where('user_id', Auth::user()->getPrimaryUserId())->pluck('name', 'id'),
            ])->render());
        }
    }


    /**
     * @param Request $request
     */
    public function deleteControlUser(Request $request)
    {
        try {
            $this->receivingControlRepository->deleteUser($request->id);
            return response()->ajax(1, '删除成功');
        } catch (CustomException $customException) {
            return response()->ajax(0, $customException->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function  deleteControlCategory(Request $request)
    {
        try {
            $this->receivingControlRepository->deleteCategory($request->id);
            return response()->ajax(1, '删除成功');
        } catch (CustomException $customException) {
            return response()->ajax(0, $customException->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function  deleteControlGoods(Request $request)
    {
        try {
            $this->receivingControlRepository->deleteGoods($request->id);
            return response()->ajax(1, '删除成功');
        } catch (CustomException $customException) {
            return response()->ajax(0, $customException->getMessage());
        }
    }
}
