<?php
namespace App\Repositories\Frontend;

use App\Exceptions\CustomException;
use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\UserReceivingUserControl;
use App\Models\UserReceivingCategoryControl;

/**
 * Class ReceivingControlRepository
 * @package App\Repositories\Frontend
 */
class ReceivingControlRepository
{
    /**
     * 用户名单
     * @param int $type 1 白名单 2 黑名单
     * @param int $otherUserId 搜索用户ID
     * @param int $pageSize 分页数
     * @return mixed
     */
    public function userList($type, $otherUserId = 0, $pageSize = 20)
    {
        return UserReceivingUserControl::where('user_id', Auth::user()->getPrimaryUserId())
            ->when($otherUserId, function ($query) use ($otherUserId) {
                return $query->where('other_user_id', $otherUserId);
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->where('type', $type)
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });
    }

    /**
     * 类别列表
     * @param integer $type 1 白名单 2 黑名单
     * @param integer $serviceId  服务ID
     * @param integer$gameId 游戏ID
     * @param int $pageSize 分页数
     * @return mixed
     */
    public function categoryList($type, $serviceId = 0, $gameId = 0, $pageSize = 20)
    {
        return UserReceivingCategoryControl::where('user_id', Auth::user()->getPrimaryUserId())
            ->when($serviceId, function ($query) use ($serviceId) {
                return $query->where('service_id', $serviceId);
            })
            ->when($gameId, function ($query) use ($gameId) {
                return $query->where('game_id', $gameId);
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->where('type', $type)
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });
    }

    /**
     * 按用户添加
     * @param $type
     * @param $otherUserId
     * @param string $remark
     * @return
     * @throws CustomException
     */
    public function addUser($type, $otherUserId, $remark = '')
    {
        if (!$otherUserId || !in_array($type, [1, 2])) {
            throw new CustomException('添加失败');
        } else {
            return UserReceivingUserControl::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'other_user_id' => $otherUserId,
                'type' => $type,
                'remark' => $remark,
            ]);
        }
    }

    /**
     * @param $type
     * @param $serviceId
     * @param $gameId
     * @param $otherUserId
     * @param string $remark
     * @return mixed
     * @throws CustomException
     */
    public function categoryAdd($type, $serviceId, $gameId, $otherUserId, $remark = '')
    {
        if (!$otherUserId || !in_array($type, [1, 2]) || !$serviceId || !$gameId) {
            throw new CustomException('添加失败');
        } else {
            return UserReceivingCategoryControl::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'service_id' => $serviceId,
                'game_id' => $gameId,
                'other_user_id' => $otherUserId,
                'type' => $type,
                'remark' => $remark,
            ]);
        }
    }
}
