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
     * @param $orderNo
     * @param int $pageSize
     */
    public function dataList($status, $orderNo, $pageSize = 15)
    {
        $userId = Auth::user()->id;
        $primaryUserId = Auth::user()->getPrimaryUserId();

        $dataList = Order::when($status == 'need', function ($query) use ($primaryUserId) {
                return $query->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' => $primaryUserId, 'status' =>4]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 4]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' => $primaryUserId, 'status' =>5]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 5]);
                });
            })
            ->when($status == 'ing', function ($query) use ($primaryUserId) {
                return $query->orWhere(function ($query) use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' =>  $primaryUserId, 'status' => 3]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 3]);
                });
            })
            ->when($status == 'finish', function ($query) use ($primaryUserId) {
                return $query->orWhere(function ($query) use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' =>  $primaryUserId, 'status' => 8]);
                })->orWhere(function ($query)  use ( $primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 8]);
                })->orWhere(function ($query) use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' =>  $primaryUserId, 'status' => 7]);
                })->orWhere(function ($query)  use ( $primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 7]);
                });
            })
            ->when($status == 'after-sales', function ($query) use ($primaryUserId) {
                return $query->orWhere(function ($query) use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' =>  $primaryUserId, 'status' => 6]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 6]);
                });
            })
            ->when($status == 'search', function ($query) use ($orderNo) {
                return $query->where('no', $orderNo);
            })
            ->when($status == 'cancel', function ($query) use ($primaryUserId) {
                return $query->orWhere(function ($query) use ($primaryUserId) {
                    $query->where(['creator_primary_user_id' =>  $primaryUserId, 'status' => 10]);
                })->orWhere(function ($query)  use ($primaryUserId) {
                    $query->where(['gainer_primary_user_id' => $primaryUserId, 'status' => 10]);
                });
            })
            ->when($status == 'market', function ($query) use ($orderNo, $userId) {
                return $query->where('status', 1);
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
                ->where('status', '>', 2);
        })->with('detail')->first();
    }
}
