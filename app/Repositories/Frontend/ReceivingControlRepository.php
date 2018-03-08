<?php
namespace App\Repositories\Frontend;

use App\Exceptions\CustomException;
use App\Models\UserReceivingGoodsControl;
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
     * @param integer $otherUserId  用户ID
     * @param integer $serviceId  服务ID
     * @param integer$gameId 游戏ID
     * @param int $pageSize 分页数
     * @return mixed
     */
    public function categoryList($type, $otherUserId, $serviceId = 0, $gameId = 0, $pageSize = 20)
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
     * 商品列表
     * @param $type
     * @param $otherUserId
     * @param int $serviceId
     * @param $goodsId
     * @param int $pageSize
     * @return mixed
     */
    public function goodsList($type, $otherUserId,  $goodsId, $pageSize = 20)
    {
        return UserReceivingGoodsControl::where('user_id', Auth::user()->getPrimaryUserId())
            ->when($goodsId, function ($query) use ($goodsId) {
                return $query->where('goods_id', $goodsId);
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
    public function addCategory($type, $serviceId, $gameId, $otherUserId, $remark = '')
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

    /**
     * 添加控制商品
     * @param $type
     * @param $otherUserId
     * @param $goodsId
     * @param string $remark
     * @return mixed
     * @throws CustomException
     */
    public function addGoods($type, $otherUserId, $goodsId, $remark = '')
    {
        if (!$otherUserId || !in_array($type, [1, 2])) {
            throw new CustomException('添加失败');
        } else {
            return UserReceivingGoodsControl::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'goods_id' => $goodsId,
                'other_user_id' => $otherUserId,
                'type' => $type,
                'remark' => $remark,
            ]);
        }
    }

    /**
     * @return string
     */
    public function deleteUser($id)
    {
        try {
            UserReceivingUserControl::where(['user_id' => Auth::user()->getPrimaryUserId(), 'id' => $id])->delete();
        } catch (CustomException $customException) {
            return $customException->getMessage();
        }
    }

    /**
     * @return string
     */
    public function deleteCategory($id)
    {
        try {
            UserReceivingCategoryControl::where(['user_id' => Auth::user()->getPrimaryUserId(), 'id' => $id])->delete();
        } catch (CustomException $customException) {
            return $customException->getMessage();
        }
    }

    /**
     * @return string
     */
    public function deleteGoods($id)
    {
        try {
            UserReceivingGoodsControl::where(['user_id' => Auth::user()->getPrimaryUserId(), 'id' => $id])->delete();
        } catch (CustomException $customException) {
            return $customException->getMessage();
        }
    }
}
