<?php
namespace App\Repositories\Frontend;

use App\Models\Game;
use App\Models\Order;
use App\Models\Service;
use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Goods;
use Illuminate\Contracts\Logging\Log;

/**
 * Class OrderRepository
 * @package App\Repositories\Frontend
 */
class OrderRepository
{

    /**
     * @param $status
     * @param $orderNO
     * @param int $pageSize
     */
    public function dataList($status, $orderNO, $pageSize = 15)
    {
        $userId = Auth::user()->id;

        $dataList = Order::when($status == 'need', function ($query) use ($status, $userId) {
            return $query->where(['creator_user_id' => $userId, 'status' => 1])
                ->orWhere(['gainer_user_id' => $userId, 'status' => 3]);
        })
            ->when($status == 'ing', function ($query) use ($orderNO, $userId) {
                return $query->where('status', 3);
            })
            ->when($status == 'finish', function ($query) use ($orderNO, $userId) {
                return $query->whereIn('status', [4, 8])
                    ->where('creator_user_id', $userId)
                    ->orWhere('gainer_user_id', $userId);
            })
            ->when($status == 'after-sales', function ($query) use ($orderNO, $userId) {
                return $query->whereIn('status', [6, 7])
                    ->where('creator_user_id', $userId)
                    ->orWhere('gainer_user_id', $userId);
            })
            ->when($status == 'market', function ($query) use ($orderNO, $userId) {
                return $query->where('status', 1);
            })
            ->when($status == 'search', function ($query) use ($orderNO) {
                return $query->where('no', $orderNO);
            })
            ->orderBy('id', 'desc')
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });
        return $dataList;
    }

    /**
     * 订单详情
     * @param $orderNo
     */
    public function detail($orderNo)
    {
        return Order::orWhere(function ($query) use ($orderNo) {
            $query->where(['creator_user_id' => Auth::user()->id, 'no' => $orderNo]);
        })->orWhere(function ($query)  use ($orderNo) {
            $query->where(['creator_primary_user_id' => Auth::user()->id, 'no' => $orderNo]);
        })->orWhere(function ($query)  use ($orderNo) {
            $query->where(['gainer_user_id' => Auth::user()->id, 'no' => $orderNo])
                ->where('status', '>', 2);
        })->orWhere(function ($query)  use ($orderNo) {
            $query->where(['gainer_primary_user_id' => Auth::user()->id, 'no' => $orderNo])
                ->where('status', '>', 2);;
        })->first();
    }
}
