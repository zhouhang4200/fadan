<?php

namespace App\Http\Controllers\Frontend\Order;

use Excel;
use App\Models\Order;
use App\Models\GoodsTemplate;
use App\Repositories\Frontend\OrderRepository;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Goods
 */
class OrderController extends Controller
{
    /**
     * 接的订单
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receive(Request $request,
        OrderRepository $orderRepository,
        ServiceRepository $serviceRepository,
        GameRepository $gameRepository
    )
    {
        $status = $request->status;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;


        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $filters = compact('serviceId', 'gameId', 'status', 'startDate', 'endDate');
        $orders  = $orderRepository->search($filters, 1);

        $fullUrl = $request->fullUrl();

        // 导出
        if ($request->export && $orders->count() > 0) {
            return $this->exportReceive($filters);
        }

        return view('frontend.v1.order.receive',
            compact('status', 'orders', 'services', 'games', 'serviceId', 'gameId', 'startDate', 'endDate', 'fullUrl'));
    }

    /**
     * 发出的订单
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function send(Request $request,
                            OrderRepository $orderRepository,
                            ServiceRepository $serviceRepository,
                            GameRepository $gameRepository
    )
    {
        $status= $request->status;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $filters = compact('serviceId', 'gameId', 'status', 'startDate', 'endDate');
        $orders  = $orderRepository->search($filters, 2);

        $fullUrl = $request->fullUrl();

        // 导出
        if ($request->export && $orders->count() > 0) {
            return $this->exportSend($filters);
        }

        return view('frontend.v1.order.send',
            compact('status', 'orders', 'services', 'games', 'serviceId', 'gameId', 'startDate', 'endDate', 'fullUrl'));
    }

    /**
     * 发送订单导出
     *
     * @param array $filters
     */
    public function exportSend($filters = [])
    {
        try {
            $orders = Order::filter($filters)
                    ->where('creator_primary_user_id', Auth::user()->getPrimaryUserId()) // 发单
                    ->latest('created_at')
                    ->with('foreignOrder')
                    ->get();

            // 标题
            $title = [
                '接单id',
                '订单号',
                '外部单号',
                '店名',
                '类型',
                '游戏',
                '商品名',
                '订单总额',
                '状态',
                '时间',
            ];
            // 数组分割,反转
            $chunkOrders = array_chunk(array_reverse($orders->toArray()), 500);

            Excel::create(iconv('UTF-8', 'gbk', '发单订单'), function ($excel) use ($chunkOrders, $title) {

                foreach ($chunkOrders as $chunkOrder) {
                    // 内容
                    $datas = [];
                    foreach ($chunkOrder as $key => $order) {
                        $datas[] = [
                            $order['gainer_primary_user_id'] ?? '--',
                            $order['no'] ?? '--',
                            $order['foreign_order']['foreign_order_no'] . "\t" ?? '--',
                            $order['foreign_order']['channel_name'] ?? '--',
                            $order['service_name'] ?? '--',
                            $order['game_name'] ?? '--',
                            $order['goods_name'] ?? '--',
                            $order['amount'] ?? 0,
                            $order['status'] ? config('order.status')[$order['status']] : '--',
                            $order['created_at'] ?? '--',
                        ];
                    }
                    // 将标题加入到数组
                    array_unshift($datas, $title);
                    // 每页多少数据
                    $excel->sheet("页数", function ($sheet) use ($datas) {
                        $sheet->rows($datas);             
                    });
                }
            })->export('xls');
            
        } catch (Exception $e) {
            
        }
    }

    /**
     * 接送订单导出
     *
     * @param array $filters
     */
    public function exportReceive($filters = [])
    {
        try {
            $orders = Order::filter($filters)
                    ->where('gainer_primary_user_id', Auth::user()->getPrimaryUserId()) // 接单
                    ->latest('created_at')
                    ->with('foreignOrder')
                    ->get();

            // 标题
            $title = [
                '发单id',
                '订单号',
                '外部单号',
                '店名',
                '类型',
                '游戏',
                '商品名',
                '订单总额',
                '状态',
                '时间',
            ];
            // 数组分割,反转
            $chunkOrders = array_chunk(array_reverse($orders->toArray()), 500);

            Excel::create(iconv('UTF-8', 'gbk', '发单订单'), function ($excel) use ($chunkOrders, $title) {

                foreach ($chunkOrders as $chunkOrder) {
                    // 内容
                    $datas = [];
                    foreach ($chunkOrder as $key => $order) {
                        $datas[] = [
                            $order['creator_primary_user_id'] ?? '--',
                            $order['no'] ?? '--',
                            $order['foreign_order']['foreign_order_no'] . "\t" ?? '--',
                            $order['foreign_order']['channel_name'] ?? '--',
                            $order['service_name'] ?? '--',
                            $order['game_name'] ?? '--',
                            $order['goods_name'] ?? '--',
                            $order['amount'] ?? 0,
                            $order['status'] ? config('order.status')[$order['status']] : '--',
                            $order['created_at'] ?? '--',
                        ];
                    }
                    // 将标题加入到数组
                    array_unshift($datas, $title);
                    // 每页多少数据
                    $excel->sheet("页数", function ($sheet) use ($datas) {
                        $sheet->rows($datas);             
                    });
                }
            })->export('xls');
            
        } catch (Exception $e) {
            
        }
    }
}
