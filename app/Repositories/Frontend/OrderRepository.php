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

        $query = Order::select(['id','no', 'foreign_order_no', 'source','status','goods_id','goods_name','service_id','service_name',
            'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id','created_at'
        ]);

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
                $query->where('status', 3);
            } elseif ($status == 'ing') {
                $query->where('status', 4);
            } elseif ($status == 'finish') {
                $query->whereIn('status', [7, 8]);
            } elseif ($status == 'after-sales') {
                $query->where('status', 6);
            } elseif ($status == 'cancel') {
                $query->where('status', 10);
            } elseif ($status == 'market') {
                $query->where('status', 1);
            } elseif ($status == 'search' && $searchType == 1) { // 按集市订单号搜索
                $query->where('no', $searchContent);
            } elseif ($status == 'search' && $searchType == 2) { // 按外部订单号搜索
                $query->where('foreign_order_no', $searchContent);
            } elseif ($status == 'search' && $searchType == 3) { // 按账号搜索
                $orderNo = OrderDetail::findOrdersBy('account', $searchContent);
                $query->whereIn('no', $orderNo);
            } elseif ($status == 'search' && $searchType == 4) { // 按备注搜索

            }
        } else {
            if ($status == 'need') {
                $query->whereIn('status', [11, 3, 5]);
            } elseif ($status == 'ing') {
                $query->whereIn('status', [3, 4]);
            } elseif ($status == 'finish') {
                $query->whereIn('status', [7, 8]);
            } elseif ($status == 'after-sales') {
                $query->where('status', 6);
            } elseif ($status == 'cancel') {
                $query->where('status', 10);
            } elseif ($status == 'market') {
                $query->where('status', 1);
            } elseif ($status == 'search' && $searchType == 1) { // 按集市订单号搜索
                $query->where('no', $searchContent);
            } elseif ($status == 'search' && $searchType == 2) { // 按外部订单号搜索
                $query->where('foreign_order_no', $searchContent);
            } elseif ($status == 'search' && $searchType == 3) { // 按账号搜索
                $orderNo = OrderDetail::findOrdersBy('account', $searchContent);
                $query->whereIn('no', $orderNo);
            } elseif ($status == 'search' && $searchType == 4) { // 按备注搜索

            }
        }
        $query->where('service_id', '!=', 4);
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
     * @param $sourceOrderNo
     * @param $gameId
     * @param $wangWang
     * @param $urgentOrder
     * @param $label
     * @param $pageSize
     * @param $startDate
     * @param $endDate
     * @param $customerServiceName
     * @return mixed
     */
    public function levelingDataList($status, $no, $sourceOrderNo, $gameId, $wangWang, $urgentOrder, $startDate, $endDate, $label, $pageSize, $customerServiceName)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->type; // 账号类型是接单还是发单

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
        }

        $query->when($status != 0, function ($query) use ($status) {
            return $query->where('status', $status);
        });
        $query->when($no != 0, function ($query) use ($no) {
            return $query->where('no', $no);
        });
        $query->when($sourceOrderNo != 0, function ($query) use ($sourceOrderNo) {
            $orderNo = OrderDetail::findOrdersBy('source_order_no', $sourceOrderNo);
            return $query->whereIn('no', $orderNo);
        });
        $query->when($gameId  != 0, function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        });
        $query->when($wangWang, function ($query) use ($wangWang, $primaryUserId) {
            $orderNo = OrderDetail::findOrdersBy('client_wang_wang', $wangWang);
            return $query->whereIn('no', $orderNo);
        });
        $query->when($customerServiceName != 0, function ($query) use ($customerServiceName) {
            $orderNo = OrderDetail::findOrdersBy('customer_service_name', $customerServiceName);
            return $query->whereIn('no', $orderNo);
        });
        $query->when($urgentOrder != 0, function ($query) use ($urgentOrder) {
            $orderNo = OrderDetail::findOrdersBy('urgent_order', $urgentOrder);
            return $query->whereIn('no', $orderNo);
        });
        $query->when(!empty($label), function ($query) use ($label) {
            $orderNo = OrderDetail::findOrdersBy('label', $label);
            return $query->whereIn('no', $orderNo);
        });
        $query->when($startDate !=0, function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when($endDate !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
        $query->where('status', '!=', 24);
        $query->where('service_id', 4)->with(['detail', 'levelingConsult']);
        $query->orderBy('id', 'desc');
        return $query->paginate($pageSize);
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
            ->with(['gainerUser', 'detail', 'history', 'foreignOrder']);

        return Excel::create('代练订单', function ($excel) use($order) {

            $excel->sheet('Sheet1', function ($sheet) use ($order) {
                $sheet->setAutoSize(true);
                $sheet->row(1, array(
                    '订单号',
                    '订单来源',
                    '标签',
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
                ));
                $order->chunk(1000, function ($items) use ($sheet) {
                    $orders = $items->toArray();
                    $data = [];

                    foreach ($orders as $k => $v) {

                        // 订单详情
                        $detail = collect($v['detail'])->pluck( 'field_value','field_name');
                        // 支付金额
                        $payment = '';
                        $haveMoney = '';
                        $poundage = '';
                        $profit = '';
                        $leftTime = '';
                        $status = '';
                        $time = '';

                        if ($v['status'] == 15 || $v['status'] == 16) {
                            $levelingConsult = LevelingConsult::where('order_no', $v['no'])->first();
                            if ($levelingConsult) {
                                $payment = $levelingConsult->amount;
                                $haveMoney = $levelingConsult->deposit;
                                $poundage = $levelingConsult->api_service ?? 0;
                            }
                        } else if(isset($detail['price_markup'])) {

                            $payment = bcadd($v['amount'], $detail['price_markup']);
                            $haveMoney = 0;
                            $poundage = 0;
                        }
                        // 利润
                        if ($v['status'] == 19 || $v['status'] == 20 || $v['status'] == 21 && isset($detail['source_price'])) {
                            $profit = bcsub(bcadd($haveMoney, bcsub($detail['source_price'], $payment)), $poundage);
                        }
                        // 如果存在接单时间
                        if (isset($orderDetail['receiving_time']) && !empty($orderDetail['receiving_time'])) {
                            // 计算到期的时间戳
                            $expirationTimestamp = strtotime($orderDetail['receiving_time']) + $orderDetail['game_leveling_day'] * 86400 + $orderDetail['game_leveling_hour'] * 3600;
                            // 计算剩余时间
                            $leftSecond = $expirationTimestamp - time();
                            $leftTime = Sec2Time($leftSecond); // 剩余时间
                        }
                        // 状态转为文字
                        if ($v['status']) {
                            $status = isset(config('order.status_leveling')[$v['status']]) ? config('order.status_leveling')[$v['status']] : config('order.status')[$v['status']];
                        }
                        if (isset($detail['game_leveling_day'])) {
                            $time = bcadd(bcmul($detail['game_leveling_day'], 8600), bcmul($detail['game_leveling_hour'], 60)) ?? 0;
                        }

                        $data[] = [
                            $v['no'],
                            $detail['order_source'] ?? '',
                            $detail['label'] ?? '',
                            $detail['cstomer_service_remark'] ?? '',
                            $detail['game_leveling_title'] ?? '',
                            $detail['game_name'] ?? '',
                            $detail['version'] ?? '',
                            $detail['serve'] ?? '',
                            $detail['game_leveling_type'] ?? '',
                            $detail['account'] ?? '',
                            $detail['password'] ?? '',
                            $detail['role'] ?? '',
                            $status ?? '',
                            $detail['source_price'] ?? '',
                            $v['amount'] ?? '',
                            $detail['security_deposit'] ?? '',
                            $detail['efficiency_deposit'] ?? '',
                            $payment,
                            $haveMoney,
                            $detail['poundage'] ?? '',
                            $profit,
                            $time,
                            $leftTime,
                            $v['created_at'] ?? '',
                            $detail['receiving_time'] ?? '',
                        ];
                    }
                    $sheet->fromArray($data, null, 'A2', false, false);
                });
            });
        })->export('xls');
    }
}
