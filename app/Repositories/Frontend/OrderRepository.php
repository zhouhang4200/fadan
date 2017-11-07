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
        $userId = Auth::user()->id; // 当前登录账号
        $type = Auth::user()->type; // 账号类型是接单还是发单
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号

        $query = Order::select(['id','no','source','status','goods_id','goods_name','service_id','service_name',
            'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id'
        ]);

        if ($userId == $primaryUserId && $status != 'market') { // 主账号默认看发出去的订单
            $query->where('creator_primary_user_id', $userId);
            $query->from(DB::raw('orders force index (orders_creator_primary_user_id_status_index)'));
        } else if ($type == 1 && $status != 'market') { // 子账号接单方
            $query->where('gainer_user_id', $userId);
            $query->from(DB::raw('orders force index (orders_gainer_user_id_status_index)'));
        } else if ($type == 2 && $status != 'market') { // 子账号发单方
            $query->where('creator_user_id', $userId);
            $query->from(DB::raw('orders force index (orders_creator_user_id_status_index)'));
        }
        // 按订单状态过滤
        if ($status == 'need') {
            $query->whereIn('status', [3, 5]);
        } elseif ($status == 'ing') {
            $query->where('status', 3);
        } elseif ($status == 'finish') {
            $query->whereIn('status', [4, 7, 8]);
        } elseif ($status == 'after-sales') {
            $query->where('status', 6);
        } elseif ($status == 'cancel') {
            $query->where('status', 10);
        } elseif ($status == 'market') {
            $query->where('status', 1);
        }
        // 除去订单集市订单所有订单按ID倒序
        if ($status != 'market') {
            $query->orderBy('id', 'desc');
        }
        return $query->paginate($pageSize);
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
