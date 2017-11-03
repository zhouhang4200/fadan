<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Exceptions\CustomException;
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
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request,ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $gameId = $request->game_id;
        $serviceId = $request->service_id;
        $otherUserId = $request->other_user_id;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();

        // 用户白名单
        $userWitheList = $this->receivingControlRepository->userList(1);
        // 用户黑名单
        $userBlacklist = $this->receivingControlRepository->userList(2);

        // 获取用户的接单权限设置
        $receivingControl = isset(Auth::user()->getUserSetting()['receiving_control']) ?
            Auth::user()->getUserSetting()['receiving_control'] : 0;

        return view('frontend.setting.receiving-control.index', compact(
            'gameId', 'serviceId', 'otherUserId', 'services', 'games',
            'userWitheList', 'userBlacklist', 'receivingControl'));
    }

    /**
     * 按用户类型添加
     * @param Request $request
     */
    public function addUser(Request $request)
    {
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

    }

    public function getControlUser(Request $request)
    {
        $type = $request->input('type', 1);
        $otherUserId = $request->input('other_user_id');

        $controlUserList = $this->receivingControlRepository->userList($type, $otherUserId, 10);

        if ($request->ajax()) {
            if (!in_array($type, [1 , 2])) {
                return response()->ajax(0, '不存在的类型');
            }
            return response()->json(\View::make('frontend.setting.receiving-control.control-user-list', [
                'controlUserList' => $controlUserList,
                'type' => $type,
                'otherUserId' => $otherUserId
            ])->render());
        }
    }

    public function getCategory(Request $request)
    {

    }
}
