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
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @return LengthAwarePaginator
     */
    public function dataList($status, $orderNo, $pageSize = 15)
    {
        $userId = Auth::user()->id;
        $primaryUserId = Auth::user()->getPrimaryUserId();

        if ($status == 'need') {
            $one = Order::where(['creator_primary_user_id' => $primaryUserId])->whereIn('status', [3, 5]);
            $two = Order::where(['gainer_primary_user_id' => $primaryUserId])->whereIn('status', [3, 5]);
            $one->unionAll($two)->orderBy('id');
            return $this->paginate($one);
        } elseif ($status == 'ing') {
            return Order::where(['creator_primary_user_id' => $primaryUserId, 'status' =>  3])->paginate(15);
        } elseif ($status == 'finish') {
            $one = Order::where(['creator_primary_user_id' => $primaryUserId])->whereIn('status', [4, 7, 8]);
            $two = Order::where(['gainer_primary_user_id' => $primaryUserId])->whereIn('status', [4, 7, 8]);
            $one->unionAll($two)->orderBy('id');
            return $this->paginate($one);
        } elseif ($status == 'after-sales') {
            $one = Order::where(['creator_primary_user_id' => $primaryUserId, 'status' => 6]);
            $two = Order::where(['gainer_primary_user_id' => $primaryUserId, 'status' => 6]);
            $one->unionAll($two)->orderBy('id');
            return $this->paginate($one);
        } elseif ($status == 'cancel') {
            $one = Order::where(['creator_primary_user_id' => $primaryUserId, 'status' => 10]);
            $two = Order::where(['gainer_primary_user_id' => $primaryUserId, 'status' => 10]);
            $one->unionAll($two)->orderBy('id');
            return $this->paginate($one);
        } elseif ($status == 'market') {
            return Order::where('status', 1)->paginate(15);
        }
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

    /**
     * 解决Laravel union 时不能分页问题
     * @param $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($query, $perPage = 15)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $statement = $query->toSql();
        foreach($query->getBindings() as $binding) {
            $statement = str_replace_first('?', gettype($binding) === "string" ? DB::connection()->getPdo()->quote($binding) : $binding, $statement);
        }
        $statement = str_replace('*', 'id', $statement);
        $count = collect(\DB::select('SELECT COUNT(*) as aggregate FROM (' . $statement . ') as items'))->pluck('aggregate')->get(0);

        return new LengthAwarePaginator(
            $query->skip(($currentPage - 1) * $perPage)->take($perPage)->get(),
            $count,
            $perPage,
            $currentPage
        );
    }
}
