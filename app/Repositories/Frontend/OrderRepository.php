<?php
namespace App\Repositories\Frontend;

use App\Models\LevelingConsult;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use DB, Auth, Excel;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Monolog\Handler\DynamoDbHandlerTest;

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
     * @param $levelingType
     * @param $pageSize
     * @return mixed
     */
    public function levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType, $pageSize = 50)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select('id','no', 'foreign_order_no', 'source','status','goods_id','goods_name','service_id',
            'service_name', 'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id','created_at','updated_at'
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

        $query->when($status != 0, function ($query) use ($status, $primaryUserId) {
            if ($status == 100) {
                return  $query->whereIn('foreign_order_no', function ($query) use($primaryUserId) {
                    $query->select('tid')
                        ->from(with(new TaobaoTrade())->getTable())
                        ->where('user_id', $primaryUserId)
                        ->where('trade_status', 3);
                });
            } else {
                return $query->where('status', $status);
            }
        });
        $query->when(!empty($no), function ($query) use ($no, $type) {
            return $query->whereIn('no', function ($query) use($no) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->whereIn('field_name', [
                        'third_order_no',
                        'source_order_no',
                        'source_order_no_1',
                        'source_order_no_2',
                        'dd373_order_no',
                        'mayi_order_no',
                        'show91_order_no',
                    ])
                    ->where('field_value', $no);
            });
        });
        $query->when($taobaoStatus  != 0, function ($query) use ($taobaoStatus) {
            return $query->whereIn('no', function ($query) use($taobaoStatus) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'taobao_status')
                    ->where('field_value', $taobaoStatus);
            });
        });
        $query->when($gameId  != 0, function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        });
        $query->when($platform != 0, function ($query) use ($platform) {
            return $query->whereIn('no', function ($query) use($platform) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'third')
                    ->where('field_value', $platform);
            });
        });
        $query->when(!empty($wangWang), function ($query) use ($wangWang) {
            return $query->whereIn('no', function ($query) use($wangWang) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'client_wang_wang')
                    ->where('field_value', $wangWang);
            });
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName) {
            return $query->whereIn('no', function ($query) use($customerServiceName) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'customer_service_name')
                    ->where('field_value', $customerServiceName);
            });
        });
        $query->when(!empty($levelingType), function ($query) use ($levelingType) {
            return $query->whereIn('no', function ($query) use($levelingType) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'game_leveling_type')
                    ->where('field_value', $levelingType);
            });
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
        $query->where('service_id', 4)->with(['detail', 'levelingConsult', 'taobaoTrade']);
        $query->orderBy('id', 'desc');

        return $query->paginate($pageSize);
    }

    /**
     * @param $status
     * @param $no
     * @param $taobaoStatus
     * @param $gameId
     * @param $wangWang
     * @param $customerServiceName
     * @param $platform
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function levelingOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType)
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
            return $query->whereIn('no', function ($query) use($no) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->whereIn('field_name', ['third_order_no', 'source_order_no'])
                    ->where('field_value', $no);
            });
        });
        $query->when($taobaoStatus  != 0, function ($query) use ($taobaoStatus) {
            return $query->whereIn('no', function ($query) use($taobaoStatus) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'taobao_status')
                    ->where('field_value', $taobaoStatus);
            });
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
            return $query->whereIn('no', function ($query) use($wangWang) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'client_wang_wang')
                    ->where('field_value', $wangWang);
            });
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName) {
            return $query->whereIn('no', function ($query) use($customerServiceName) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'customer_service_name')
                    ->where('field_value', $customerServiceName);
            });
        });
        $query->when(!empty($levelingType), function ($query) use ($levelingType) {
            return $query->whereIn('no', function ($query) use($levelingType) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'game_leveling_type')
                    ->where('field_value', $levelingType);
            });
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
        $query->groupBy('status');
        $statusCount = $query->pluck('count', 'status');

        return $statusCount;
    }

    /**
     * 淘宝退款订单数量统计
     * @param $status
     * @param $no
     * @param $taobaoStatus
     * @param $gameId
     * @param $wangWang
     * @param $customerServiceName
     * @param $platform
     * @param $startDate
     * @param $endDate
     * @param $levelingType
     * @return mixed
     */
    public function levelingTaobaoRefundOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select('id,no, foreign_order_no, source,status,goods_id,goods_name,service_id,
            service_name, game_id,game_name,original_price,price,quantity,original_amount,amount,remark,
            creator_user_id,creator_primary_user_id,gainer_user_id,gainer_primary_user_id,created_at, status');

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
            return $query->whereIn('no', function ($query) use($no) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->whereIn('field_name', ['third_order_no', 'source_order_no'])
                    ->where('field_value', $no);
            });
        });
        $query->whereIn('foreign_order_no', function ($query) use($primaryUserId) {
            $query->select('tid')
                ->from(with(new TaobaoTrade())->getTable())
                ->where('user_id', $primaryUserId)
                ->where('trade_status', 3);
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
            return $query->whereIn('no', function ($query) use($wangWang) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'client_wang_wang')
                    ->where('field_value', $wangWang);
            });
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName) {
            return $query->whereIn('no', function ($query) use($customerServiceName) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'customer_service_name')
                    ->where('field_value', $customerServiceName);
            });
        });
        $query->when(!empty($levelingType), function ($query) use ($levelingType) {
            return $query->whereIn('no', function ($query) use($levelingType) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'game_leveling_type')
                    ->where('field_value', $levelingType);
            });
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });

        return $query->count();
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
     * @param $status
     * @param $no
     * @param $taobaoStatus
     * @param $gameId
     * @param $wangWang
     * @param $customerServiceName
     * @param $platform
     * @param $startDate
     * @param $endDate
     * @param $levelingType
     */
    public function levelingExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select('id','no', 'foreign_order_no', 'source','status','goods_id','goods_name','service_id',
            'service_name', 'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id','created_at','updated_at'
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

        $query->when($status != 0, function ($query) use ($status, $primaryUserId) {
            if ($status == 100) {
                return  $query->whereIn('foreign_order_no', function ($query) use($primaryUserId) {
                    $query->select('tid')
                        ->from(with(new TaobaoTrade())->getTable())
                        ->where('user_id', $primaryUserId)
                        ->where('trade_status', 3);
                });
            } else {
                return $query->where('status', $status);
            }
        });
        $query->when(!empty($no), function ($query) use ($no, $type) {
            return $query->whereIn('no', function ($query) use($no) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->whereIn('field_name', [
                        'third_order_no',
                        'source_order_no',
                        'source_order_no_1',
                        'source_order_no_2',
                        'dd373_order_no',
                        'mayi_order_no',
                        'show91_order_no',
                    ])
                    ->where('field_value', $no);
            });
        });
        $query->when($taobaoStatus  != 0, function ($query) use ($taobaoStatus) {
            return $query->whereIn('no', function ($query) use($taobaoStatus) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'taobao_status')
                    ->where('field_value', $taobaoStatus);
            });
        });
        $query->when($gameId  != 0, function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        });
        $query->when($platform != 0, function ($query) use ($platform) {
            return $query->whereIn('no', function ($query) use($platform) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'third')
                    ->where('field_value', $platform);
            });
        });
        $query->when(!empty($wangWang), function ($query) use ($wangWang) {
            return $query->whereIn('no', function ($query) use($wangWang) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'client_wang_wang')
                    ->where('field_value', $wangWang);
            });
        });
        $query->when(!empty($customerServiceName), function ($query) use ($customerServiceName) {
            return $query->whereIn('no', function ($query) use($customerServiceName) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'customer_service_name')
                    ->where('field_value', $customerServiceName);
            });
        });
        $query->when(!empty($levelingType), function ($query) use ($levelingType) {
            return $query->whereIn('no', function ($query) use($levelingType) {
                $query->select('order_no')
                    ->from(with(new OrderDetail())->getTable())
                    ->where('field_name', 'game_leveling_type')
                    ->where('field_value', $levelingType);
            });
        });
        $query->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        });
        $query->when(!empty($endDate) !=0, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate. " 23:59:59");
        });
        $query->where('service_id', 4)->with(['detail', 'levelingConsult', 'taobaoTrade']);
        $query->orderBy('id', 'desc');

        return export([
            '店铺名',
            '淘宝单号',
            '接单平台',
            '订单状态',
            '玩家旺旺',
            '客服备注',
            '代练标题',
            '游戏',
            '区',
            '服',
            '账号',
            '密码',
            '角色名称',
            '代练价格',
            '效率保证金',
            '安全保证金',
            '发单时间',
            '接单时间',
            '代练时间',
            '剩余时间',
            '打手QQ电话',
            '号主电话',
            '来源价格',
            '支付代练费用',
            '获得赔偿金额',
            '手续费',
            '最终支付金额',
            '发单客服',
        ], '订单导出', $query, function ($order, $out){
            $order->chunk(300, function ($items) use ($out) {
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

                    if (!in_array($orderInfo['status'], [19, 20, 21])){
                        $orderCurrent['payment_amount'] = '';
                        $orderCurrent['get_amount'] = '';
                        $orderCurrent['poundage'] = '';
                        $orderCurrent['profit'] = '';
                    } else {
                        // 支付金额
                        $amount = 0;
                        if (in_array($orderInfo['status'], [21, 19])) {
                            $amount = $orderInfo['leveling_consult']['api_amount'];
                        } else {
                            $amount = $orderInfo['amount'];
                        }
                        // 支付金额
                        $orderCurrent['payment_amount'] = $amount !=0 ?  $amount + 0:  $amount;

                        $orderCurrent['payment_amount'] = (float)$orderCurrent['payment_amount'] + 0;
                        $orderCurrent['get_amount'] = (float)$orderCurrent['get_amount'] + 0;
                        $orderCurrent['poundage'] = (float)$orderCurrent['poundage'] + 0;
                        // 利润
                        $orderCurrent['profit'] = ($orderCurrent['get_amount']  - $orderCurrent['payment_amount']  - $orderCurrent['poundage']) + 0;
                    }

                    $days = $orderCurrent['game_leveling_day'] ?? 0;
                    $hours = $orderCurrent['game_leveling_hour'] ?? 0;
                    $orderCurrent['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

                    // 如果存在接单时间
                    $orderCurrent['time_out'] = 0;
                    if (isset($orderCurrent['receiving_time']) && !empty($orderCurrent['receiving_time'])) {
                        // 计算到期的时间戳
                        $expirationTimestamp = strtotime($orderCurrent['receiving_time']) + $days * 86400 + $hours * 3600;
                        // 计算剩余时间
                        $leftSecond = $expirationTimestamp - time();
                        $orderCurrent['left_time'] = sec2Time($leftSecond); // 剩余时间
                        if ($leftSecond < 0) {
                            $orderCurrent['timeout'] = 1;
                            $orderCurrent['timeout_time'] = sec2Time(abs($leftSecond)); // 超时时间
                        }
                    } else {
                        $orderCurrent['left_time'] = '';
                    }
                    // 按状态显示不同时间文案
                    $orderCurrent['status_time'] = '结束';
                    if ($orderInfo['status'] == 1) {
                        $orderCurrent['status_time'] =  sec2Time(time() - strtotime($orderInfo['created_at']));
                    }  elseif ($orderInfo['status'] == 13) {
                        $orderCurrent['status_time'] =  sec2Time(time() - strtotime($orderCurrent['receiving_time']));
                    }

                    // 接单平台名字
                    $orderCurrent['third_name'] = '';
                    if (isset($orderCurrent['third']) && isset(config('partner.platform')[(int)$orderCurrent['third']])) {
                        $orderCurrent['third_name'] = config('partner.platform')[$orderCurrent['third']]['name'];
                    }

                    // 订单超过12小时
                    $currentTime = new Carbon();
                    $orderTime = $currentTime->parse($orderCurrent['created_at']);
                    $orderCurrent['day'] = $orderTime->diffInDays($currentTime, false);
                    $orderCurrent['password'] = str_replace(substr($orderCurrent['password'], -4, 4), '****', $orderCurrent['password']);
                    $orderCurrent['amount'] = intval($orderCurrent['amount']) == $orderCurrent['amount'] ? intval($orderCurrent['amount']) : $orderCurrent['amount'];


                    // 如果是接单账号则隐藏:玩家旺旺、客服备注、来源价格、利润 等字段数据
                    if (auth()->user()->getPrimaryUserId() == $orderCurrent['gainer_primary_user_id']) {
                        $orderCurrent['profit'] = '';
                        $orderCurrent['client_wang_wang'] = '';
                        $orderCurrent['seller_nick'] = '';
                        $orderCurrent['source_price'] = '';
                        $orderCurrent['customer_service_remark'] = '';
                    }

                    $data = [
                        $orderCurrent['seller_nick'],
                        $orderCurrent['source_order_no'] . "\t",
                        $orderCurrent['third_name'] ? $orderCurrent['third_name'] . ':' . $orderCurrent['third_order_no'] . "\t": '',
                        $orderCurrent['status_text'] ?? '-',
                        $orderCurrent['client_wang_wang'] ?? '-',
                        $orderCurrent['customer_service_remark'] ?? '-',
                        $orderCurrent['game_leveling_title'] ?? '-',
                        $orderCurrent['game_name'] ?? '-',
                        $orderCurrent['region'] ?? '-',
                        $orderCurrent['serve'] ?? '-',
                        $orderCurrent['account'] ?? '-',
                        $orderCurrent['password'] ?? '-',
                        $orderCurrent['role'] ?? '-',
                        $orderCurrent['amount'] ?? '-',
                        $orderCurrent['efficiency_deposit'] ?? '-',
                        $orderCurrent['security_deposit'] ?? '-',
                        $orderCurrent['created_at'] ?? '-',
                        $orderCurrent['receiving_time'] ?? '-',
                        $orderCurrent['leveling_time']  ?? '-',
                        $orderCurrent['left_time']  ?? '-',
                        $orderCurrent['hatchet_man_qq']  ?? '-',
                        $orderCurrent['client_phone']  ?? '-',
                        $orderCurrent['source_price']  ?? '-',
                        $orderCurrent['payment_amount']  ?? '-',
                        $orderCurrent['get_amount']  ?? '-',
                        $orderCurrent['poundage']  ?? '-',
                        $orderCurrent['profit']  ?? '-',
                        $orderCurrent['customer_service_name']  ?? '-',
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }

    /**
     * 财务订单列表导出
     * @param  [type]  $status              [description]
     * @param  [type]  $no                  [description]
     * @param  [type]  $taobaoStatus        [description]
     * @param  [type]  $gameId              [description]
     * @param  [type]  $wangWang            [description]
     * @param  [type]  $customerServiceName [description]
     * @param  [type]  $platform            [description]
     * @param  [type]  $startDate           [description]
     * @param  [type]  $endDate             [description]
     * @param  string  $sellerNick          [description]
     * @param  integer $pageSize            [description]
     * @return [type]                       [description]
     */
    public function levelingDataListExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick = '', $pageSize = 50)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId(); // 当前账号的主账号
        $type = Auth::user()->leveling_type; // 账号类型是接单还是发单

        $query = Order::select('id','no', 'foreign_order_no', 'source','status','goods_id','goods_name','service_id',
            'service_name', 'game_id','game_name','original_price','price','quantity','original_amount','amount','remark',
            'creator_user_id','creator_primary_user_id','gainer_user_id','gainer_primary_user_id','created_at','updated_at'
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

        return $query;
    }
}
