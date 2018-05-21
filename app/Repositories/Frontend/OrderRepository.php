<?php
namespace App\Repositories\Frontend;

use App\Models\LevelingConsult;
use App\Models\OrderDetail;
use DB, Auth, Excel;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class OrderRepository
 * @package App\Repositories\Frontend
 */
class OrderRepository
{
    /**
     * @param $status
     * @param $searchType
     * @param $searchContent
     * @param int $pageSize
     */
    public function dataList($status, $searchType, $searchContent, $pageSize = 15)
    {
        $userId = Auth::user()->id; // 当前登录账号
        $type = Auth::user()->type; // 账号类型是接单还是发单
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号

        $query = Order::select(['orders.id','orders.no', 'orders.foreign_order_no', 'orders.source','orders.status','orders.goods_id','orders.goods_name','orders.service_id','orders.service_name',
                'orders.game_id','orders.game_name','orders.original_price','orders.price','orders.quantity','orders.original_amount','orders.amount','orders.remark',
                'orders.creator_user_id','orders.creator_primary_user_id','orders.gainer_user_id','orders.gainer_primary_user_id','orders.created_at', 'foreign_orders.wang_wang'
            ])
            ->leftJoin('foreign_orders', 'foreign_orders.foreign_order_no', 'orders.foreign_order_no');

//        if ($userId == $primaryUserId && $status != 'market') { // 主账号
        if ( $status != 'market') { // 主账号
            if ($type == 1) {
                $query->where('gainer_primary_user_id', $primaryUserId); // 接单
            } else {
                $query->where('creator_primary_user_id', $primaryUserId); // 发单
            }
            $query->orderBy('id', 'desc');
        } 
//            $query->from(DB::raw('orders force index (orders_creator_primary_user_id_status_index)'));
//        } else if ($type == 1 && $status != 'market') { // 子账号接单方
//            $query->where('gainer_user_id', $userId);
//            $query->from(DB::raw('orders force index (orders_gainer_user_id_status_index)'));
//        } else if ($type == 2 && $status != 'market') { // 子账号发单方
//            $query->where('creator_user_id', $userId);
//            $query->from(DB::raw('orders force index (orders_creator_user_id_status_index)'));
//        }
        // 按订单状态过滤

        if ($type == 1) { // 接单方
            if ($status == 'need') {
                $query->where('orders.status', 3);
            } elseif ($status == 'ing') {
                $query->where('orders.status', 4);
            } elseif ($status == 'finish') {
                $query->whereIn('orders.status', [7, 8]);
            } elseif ($status == 'after-sales') {
                $query->where('orders.status', 6);
            } elseif ($status == 'cancel') {
                $query->where('orders.status', 10);
            } elseif ($status == 'market') {
                $query->where('orders.status', 1);
            } elseif ($status == 'search' && $searchType == 1) { // 按集市订单号搜索
                $query->where('orders.no', $searchContent);
            } elseif ($status == 'search' && $searchType == 2) { // 按外部订单号搜索
                $query->where('orders.foreign_order_no', $searchContent);
            } elseif ($status == 'search' && $searchType == 3) { // 按账号搜索
                $orderNo = OrderDetail::findOrdersBy('account', $searchContent, 1);
                $query->whereIn('orders.no', $orderNo);
            } elseif ($status == 'search' && $searchType == 4) { // 按备注搜索

            }
        } else {
            if ($status == 'need') {
                $query->whereIn('orders.status', [11, 3, 5]);
            } elseif ($status == 'ing') {
                $query->whereIn('orders.status', [3, 4]);
            } elseif ($status == 'finish') {
                $query->whereIn('orders.status', [7, 8]);
            } elseif ($status == 'after-sales') {
                $query->where('orders.status', 6);
            } elseif ($status == 'cancel') {
                $query->where('orders.status', 10);
            } elseif ($status == 'market') {
                $query->where('orders.status', 1);
            } elseif ($status == 'search' && $searchType == 1) { // 按集市订单号搜索
                $query->where('orders.no', $searchContent);
            } elseif ($status == 'search' && $searchType == 2) { // 按外部订单号搜索
                $query->where('orders.foreign_order_no', $searchContent);
            } elseif ($status == 'search' && $searchType == 3) { // 按账号搜索
                $orderNo = OrderDetail::findOrdersBy('account', $searchContent);
                $query->whereIn('orders.no', $orderNo);
            } elseif ($status == 'search' && $searchType == 4) { // 按备注搜索

            }
        }
        $query->where('orders.service_id', '!=', 4);
        return $query->paginate($pageSize);
    }

    /**
     * 订单详情
     * @param $orderNo
     */
    public function detail($orderNo)
    {
//        return Order::orWhere(function ($query) use ($orderNo) {
//            $query->where(['creator_user_id' => Auth::user()->id, 'no' => $orderNo]);
//        })->orWhere(function ($query)  use ($orderNo) {
//            $query->where(['creator_primary_user_id' => Auth::user()->id, 'no' => $orderNo]);
//        })->orWhere(function ($query)  use ($orderNo) {
//            $query->where(['gainer_user_id' => Auth::user()->id, 'no' => $orderNo])
//                ->where('status', '>', 2);
//        })->orWhere(function ($query)  use ($orderNo) {
//            $query->where(['gainer_primary_user_id' => Auth::user()->id, 'no' => $orderNo])
//                ->where('status', '>', 2);
        $primaryUserId = Auth::user()->getPrimaryUserId();
        return Order::orWhere(function ($query) use ($orderNo, $primaryUserId) {
            $query->where(['creator_primary_user_id' => $primaryUserId, 'no' => $orderNo]);
        })->orWhere(function ($query)  use ($orderNo, $primaryUserId) {
            $query->where(['gainer_primary_user_id' => $primaryUserId, 'no' => $orderNo])
                ->where('status', '>', 2);
        })->with(['detail', 'foreignOrder'])->first();
    }

    /**
     * 订单搜索
     * @param  integer $type 1 接单方 2 发单方
     * @param  array $condition 搜索条件
     * @param  integer $pageSize 每页数量
     */
    public function search($condition, $type = 1, $pageSize = 10)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号

        $query = Order::filter($condition);
        if ($type == 1) {
            $query->where('gainer_primary_user_id', $primaryUserId); // 接单
        } else {
            $query->where('creator_primary_user_id', $primaryUserId); // 发单
        }
        return $query->paginate($pageSize);
    }

    /**
     * 代练订单
     * @param $status
     * @param $no
     * @param $taobaoStatus
     * @param $gameId
     * @param $wangWang
     * @param $customerServiceName
     * @param $platform
     * @param $startDate
     * @param $endDate
     * @param $sellerNick
     * @param $pageSize
     * @return mixed
     */
    public function levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick = '', $pageSize = 50)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select('id','no', 'foreign_order_no', 'source','status','goods_id','goods_name','service_id',
            'service_name', 'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id','created_at'
        );

        if ($status != 1) {
            if ($type == 1) {
                $query->where('gainer_primary_user_id', $primaryUserId); // 接单
            } else {
                $query->where('creator_primary_user_id', $primaryUserId); // 发单
            }
        } else {
            $query->where('creator_primary_user_id', $primaryUserId); // 发单
        }

        $query->when($status != 0, function ($query) use ($status) {
            return $query->where('status', $status);
        });
        $query->when(!empty($no), function ($query) use ($no, $type) {
            $thirdOrder = OrderDetail::findOrdersBy('third_order_no', $no, $type);
            $foreignOrder = OrderDetail::findOrdersBy('source_order_no', $no, $type);

            return $query->whereIn('no', array_merge($thirdOrder, $foreignOrder, [$no]));
        });
        $query->when($taobaoStatus  != 0, function ($query) use ($taobaoStatus, $type) {
            $foreignOrder = OrderDetail::findOrdersBy('taobao_status', $taobaoStatus, $type);

            return $query->whereIn('no', $foreignOrder);
        });
        $query->when($gameId  != 0, function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        });
        $query->when($platform != 0, function ($query) use ($platform, $type) {
//            $orderNoArr = [];
//            $orderNo = OrderDetail::findOrdersBy('third', $platform, $type);
//            if ($orderNo) {
//                $orderNoArr = $orderNo;
//            } else {
//                $orderNoArr = [999];
//            }
//            return $query->whereIn('no', $orderNoArr);
            return $query->whereIn('no', function ($query) use($platform) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'third')
                    ->where('field_value', $platform);
            });
        });
        $query->when(!empty($sellerNick), function ($query) use ($sellerNick, $primaryUserId, $type) {
            $orderNo = OrderDetail::findOrdersBy('seller_nick', $sellerNick, $type);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($wangWang), function ($query) use ($wangWang, $primaryUserId, $type) {
            $orderNo = OrderDetail::findOrdersBy('client_wang_wang', $wangWang, $type);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName, $type) {
            $orderNo = OrderDetail::findOrdersBy('customer_service_name', $customerServiceName, $type);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
//        $query->where('status', '!=', 24);
        $query->where('service_id', 4)->with(['detail', 'levelingConsult']);
        $query->orderBy('id', 'desc');

        $data = $query->paginate($pageSize);

        return $data;
    }

    public function levelingOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select(\DB::raw('id,no, foreign_order_no, source,status,goods_id,goods_name,service_id,
            service_name, game_id,game_name,original_price,price,quantity,original_amount,amount,remark,
            creator_user_id,creator_primary_user_id,gainer_user_id,gainer_primary_user_id,created_at, status, count(1) as count'));

        if ($status != 1) {
            if ($type == 1) {
                $query->where('gainer_primary_user_id', $primaryUserId); // 接单
            } else {
                $query->where('creator_primary_user_id', $primaryUserId); // 发单
            }
        } else {
            $query->where('creator_primary_user_id', $primaryUserId); // 发单
        }


        $query->when(!empty($no), function ($query) use ($no, $type) {
            $thirdOrder = OrderDetail::findOrdersBy('third_order_no', $no, $type);
            $foreignOrder = OrderDetail::findOrdersBy('source_order_no', $no, $type);

            return $query->whereIn('no', array_merge($thirdOrder, $foreignOrder));
        });
        $query->when($taobaoStatus  != 0, function ($query) use ($taobaoStatus, $type) {
            $foreignOrder = OrderDetail::findOrdersBy('taobao_status', $taobaoStatus, $type);

            return $query->whereIn('no', $foreignOrder);
        });
        $query->when($gameId  != 0, function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        });
        $query->when($platform != 0, function ($query) use ($platform, $type) {
            return $query->whereIn('no', function ($query) use($platform) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'third')
                    ->where('field_value', $platform);
            });
        });
        $query->when(!empty($wangWang), function ($query) use ($wangWang, $primaryUserId, $type) {
            $orderNo = OrderDetail::findOrdersBy('client_wang_wang', $wangWang, $type);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName, $type) {
            $orderNo = OrderDetail::findOrdersBy('customer_service_name', $customerServiceName, $type);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
//        $query->where('status', '!=', 24);
        $query->where('service_id', 4)->with(['detail']);
        $query->groupBy('status');
        return $query->pluck('count', 'status');
    }
    /**
     * 代练订单详情
     * @param $orderNo
     * @return array
     */
    public function levelingDetail($orderNo)
    {
        $order = Order::where('no', $orderNo)->with(['detail', 'foreignOrder', 'levelingConsult'])->first();
        return  array_merge($order->detail->pluck('field_value', 'field_name')->toArray(), $order->toArray());
    }

    /**
     * 代练订单数据导出
     * @param $filters
     * @return mixed
     */
    public function levelingExport($filters)
    {
        $order = Order::select([
            'creator_primary_user_id',
            'gainer_primary_user_id',
            'no',
            'foreign_order_no',
            'source',
            'status',
            'service_name',
            'game_name',
            'goods_name',
            'quantity',
            'original_price',
            'original_amount',
            'price',
            'amount',
            'created_at',
        ])
            ->filter($filters)
            ->where('status', '!=', 24)
            ->where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
            ->with(['gainerUser', 'detail',  'levelingConsult']);

        export([
            '订单号',
            '订单来源',
            '客服备注',
            '代练标题',
            '游戏',
            '区',
            '服',
            '代练类型',
            '账号',
            '密码',
            '角色名称',
            '订单状态',
            '来源价格',
            '代练价格',
            '安全保证金',
            '效率保证金',
            '支付金额',
            '获得金额',
            '手续费',
            '利润',
            '代练时间',
            '剩余时间',
            '发单时间',
            '接单时间',
        ], '订单导出', $order, function ($order, $out){
            $order->chunk(1000, function ($items) use ($out) {
                foreach ($items as $item) {
                    $orderInfo = $item->toArray();

                    // 删掉无用的数据
                    unset($orderInfo['detail']);

                    $orderInfo['status_text'] = config('order.status_leveling')[$orderInfo['status']] ?? '';
                    $orderInfo['master'] = $orderInfo['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
                    $orderInfo['consult'] = $orderInfo['leveling_consult']['consult'] ?? '';
                    $orderInfo['complain'] = $orderInfo['leveling_consult']['complain'] ?? '';

                    // 当前订单数据
                    $orderCurrent = array_merge($item->detail->pluck('field_value', 'field_name')->toArray(), $orderInfo);

                    if (!in_array($orderInfo['status'], [19, 20, 21])) {
                        $orderCurrent['payment_amount'] = '';
                        $orderCurrent['get_amount'] = '';
                        $orderCurrent['poundage'] = '';
                        $orderCurrent['profit'] = '';
                    } else {
                        // 支付金额
                        if ($orderInfo['status'] == 21) {
                            $amount = $orderInfo['leveling_consult']['api_amount'];
                        } else {
                            $amount = $orderInfo['leveling_consult']['amount'];
                        }
                        // 支付金额
                        $orderCurrent['payment_amount'] = $amount != 0 ? $amount + 0 : $orderInfo['amount'] + 0;

                        $orderCurrent['payment_amount'] = (float)$orderCurrent['payment_amount'] + 0;
                        $orderCurrent['get_amount'] = (float)$orderCurrent['get_amount'] + 0;
                        $orderCurrent['poundage'] = (float)$orderCurrent['poundage'] + 0;
                        // 利润
                        $orderCurrent['profit'] = ((float)$orderCurrent['source_price'] - $orderCurrent['payment_amount'] + $orderCurrent['get_amount'] - $orderCurrent['poundage']) + 0;
                    }

                    $days = $orderCurrent['game_leveling_day'] ?? 0;
                    $hours = $orderCurrent['game_leveling_hour'] ?? 0;
                    $orderCurrent['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

                    // 如果存在接单时间
                    if (isset($orderCurrent['receiving_time']) && !empty($orderCurrent['receiving_time'])) {
                        // 计算到期的时间戳
                        $expirationTimestamp = strtotime($orderCurrent['receiving_time']) + $days * 86400 + $hours * 3600;
                        // 计算剩余时间
                        $leftSecond = $expirationTimestamp - time();
                        $orderCurrent['left_time'] = Sec2Time($leftSecond); // 剩余时间
                    } else {
                        $orderCurrent['left_time'] = '';
                    }
                    $data = [
                        $orderCurrent['no'] . "\t",
                        $orderCurrent['order_source'] . "\t",
                        $orderCurrent['customer_service_remark'] ?? "",
                        $orderCurrent['game_leveling_title'],
                        $orderCurrent['game_name'],
                        $orderCurrent['region'],
                        $orderCurrent['serve'],
                        $orderCurrent['game_leveling_type'],
                        $orderCurrent['account'],
                        $orderCurrent['password'],
                        $orderCurrent['role'],
                        $orderCurrent['status_text'],
                        $orderCurrent['source_price'],
                        $orderCurrent['amount'],
                        $orderCurrent['security_deposit'],
                        $orderCurrent['efficiency_deposit'],
                        (string)$orderCurrent['payment_amount'],
                        (string)$orderCurrent['get_amount'],
                        (string)$orderCurrent['poundage'],
                        (string)$orderCurrent['profit'],
                        $orderCurrent['leveling_time'],
                        $orderCurrent['left_time'],
                        $orderCurrent['created_at'] ?? '',
                        $orderCurrent['receiving_time'] ?? '',
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }
}
