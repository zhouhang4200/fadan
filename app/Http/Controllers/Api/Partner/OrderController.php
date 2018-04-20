<?php
namespace App\Http\Controllers\Api\Partner;


use Carbon\Carbon;
use App\Models\LevelingMessage;
use App\Repositories\Frontend\OrderDetailRepository;
use Order, DB, Exception;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\LevelingConsult;
use App\Models\Order as OrderModel;
use App\Http\Controllers\Controller;
use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use Psy\Test\Exception\RuntimeExceptionTest;

/**
 * Class OrderController
 * @package App\Http\Controllers\Api\Partner
 */
class OrderController extends Controller
{
    /**
     * 获取订单和订单详情各个字段组成的对象
     * @param  [type] $orderNo [description]
     * @return [type]    $orderData      [object]
     */
    public function getOrderAndOrderDetails($orderNo)
    {
        $order = OrderDetail::where('field_value', $orderNo)->first();

        if (!$order) {
            throw new DailianException('订单号不存在!');
        }
        $orderData = collect(OrderDetailRepository::getByOrderNo($order->order_no))->toJson();

        return json_decode($orderData);
        $array =  DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS PASSWORD,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_title' THEN a.field_value ELSE '' END) AS game_leveling_title,
                MAX(CASE WHEN a.field_name='game_leveling_instructions' THEN a.field_value ELSE '' END) AS game_leveling_instructions,
                MAX(CASE WHEN a.field_name='game_leveling_requirements' THEN a.field_value ELSE '' END) AS game_leveling_requirements,
                MAX(CASE WHEN a.field_name='auto_unshelve_time' THEN a.field_value ELSE '' END) AS auto_unshelve_time,
                MAX(CASE WHEN a.field_name='game_leveling_amount' THEN a.field_value ELSE '' END) AS game_leveling_amount,
                MAX(CASE WHEN a.field_name='game_leveling_day' THEN a.field_value ELSE '' END) AS game_leveling_day,
                MAX(CASE WHEN a.field_name='game_leveling_hour' THEN a.field_value ELSE '' END) AS game_leveling_hour,
                MAX(CASE WHEN a.field_name='security_deposit' THEN a.field_value ELSE '' END) AS security_deposit,
                MAX(CASE WHEN a.field_name='efficiency_deposit' THEN a.field_value ELSE '' END) AS efficiency_deposit,
                MAX(CASE WHEN a.field_name='user_phone' THEN a.field_value ELSE '' END) AS user_phone,
                MAX(CASE WHEN a.field_name='user_qq' THEN a.field_value ELSE '' END) AS user_qq,
                MAX(CASE WHEN a.field_name='source_price' THEN a.field_value ELSE '' END) AS source_price,
                MAX(CASE WHEN a.field_name='client_name' THEN a.field_value ELSE '' END) AS client_name,
                MAX(CASE WHEN a.field_name='client_phone' THEN a.field_value ELSE '' END) AS client_phone,
                MAX(CASE WHEN a.field_name='client_qq' THEN a.field_value ELSE '' END) AS client_qq,
                MAX(CASE WHEN a.field_name='client_wang_wang' THEN a.field_value ELSE '' END) AS client_wang_wang,
                MAX(CASE WHEN a.field_name='game_leveling_require_day' THEN a.field_value ELSE '' END) AS game_leveling_require_day,
                MAX(CASE WHEN a.field_name='game_leveling_require_hour' THEN a.field_value ELSE '' END) AS game_leveling_require_hour,
                MAX(CASE WHEN a.field_name='customer_service_remark' THEN a.field_value ELSE '' END) AS customer_service_remark,
                MAX(CASE WHEN a.field_name='receiving_time' THEN a.field_value ELSE '' END) AS receiving_time,
                MAX(CASE WHEN a.field_name='checkout_time' THEN a.field_value ELSE '' END) AS checkout_time,
                MAX(CASE WHEN a.field_name='customer_service_name' THEN a.field_value ELSE '' END) AS customer_service_name,
                MAX(CASE WHEN a.field_name='third_order_no' THEN a.field_value ELSE '' END) AS third_order_no,
                MAX(CASE WHEN a.field_name='third' THEN a.field_value ELSE '' END) AS third,
                MAX(CASE WHEN a.field_name='poundage' THEN a.field_value ELSE '' END) AS poundage,
                MAX(CASE WHEN a.field_name='price_markup' THEN a.field_value ELSE '' END) AS price_markup,
                MAX(CASE WHEN a.field_name='show91_order_no' THEN a.field_value ELSE '' END) AS show91_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            WHERE a.order_no=(SELECT order_no FROM order_details WHERE field_value='$orderNo' limit 1)");
            
            if (isset($array) && is_array($array)) {
                return $array[0];
            }
            return response()->ajax(0, '接口错误，请重试');
    }

    /**
     * 查询订单信息
     * @param Request $request
     */
    public function query(Request $request)
    {
    }

    /**接单
     * @param Request $request
     */
    public function receive(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            if (! isset($request->hatchet_man_qq) || ! isset($request->hatchet_man_phone) || ! isset($request->hatchet_man_name)) {
                return response()->partner(0, '打手信息缺失');
            }
            if ($orderData) {
                // 外部平台调用我们的接单操作
                DailianFactory::choose('receive')->run($orderData->no, $request->user->id, true);
                // 写入打手信息(QQ, 电话， 昵称)
                OrderDetail::where('order_no', $orderData->no)
                    ->where('field_name', 'hatchet_man_qq')
                    ->update(['field_value' => $request->hatchet_man_qq]);

                OrderDetail::where('order_no', $orderData->no)
                    ->where('field_name', 'hatchet_man_phone')
                    ->update(['field_value' => $request->hatchet_man_phone]);

                OrderDetail::where('order_no', $orderData->no)
                    ->where('field_name', 'hatchet_man_name')
                    ->update(['field_value' => $request->hatchet_man_name]);

            } else {
                return response()->partner(0, '订单不存在');
            }
        } catch (DailianException $e) {
            DB::rollback();
            mylog('order.operate.receive', ['订单号' => $orderData->no, '结果' => '失败', '原因' => $e->getMessage()]);
            return response()->partner(0, $e->getMessage());
        }  catch (Exception $e) {
            DB::rollback();
            mylog('order.operate.receive', ['订单号' => $orderData->no, '结果' => '失败', '原因' => $e->getMessage()]);
            return response()->partner(0, '接口异常');
        } 
        DB::commit();
        return response()->partner(1, '成功');
    }

    /**
     * 申请验收
     * @param Request $request
     */
    public function applyComplete(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('applyComplete')->run($orderData->no, $request->user->id);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        } 
    }

    /**
     * 取消验收
     * @param Request $request
     */
    public function cancelComplete(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('cancelComplete')->run($orderData->no, $request->user->id);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        } 
    }

    /**
     * 撤销
     * @param Request $request
     */
    public function revoke(Request $request)
    {
        DB::beginTransaction();
        try {
            // 接口传过来的参数
            $apiAmount  = $request->input('api_amount', '');  // 回传代练费
            $apiDeposit = $request->input('api_deposit', ''); // 回传双金
            $apiService = $request->input('api_service', ''); // 回传手续费
            $content    = $request->input('content', '无'); // 回传的撤销说明
            // 判断传入的金额是否合法
            if (! is_numeric($apiAmount) || ! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
                return response()->partner(0, '代练费和双金或手续费必须是数字');
            }

            if ($apiAmount < 0 || $apiDeposit < 0 || $apiService < 0) {
                return response()->partner(0, '代练费和双金或手续费必须大于0');
            }
            // 订单信息
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            // 订单双金之和
            $orderDeposit  = bcadd($orderData->security_deposit, $orderData->efficiency_deposit);
            $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
            $isOverAmount  = bcsub($orderData->amount, $apiAmount);
            // 写入双金与订单双击比较
            if ($isOverDeposit < 0) {
                return response()->partner(0, '传入双金超过订单代练双金');
            }
            // 写入代练费与订单代练费比较
            if ($isOverAmount < 0) {
                return response()->partner(0, '传入代练费超过订单代练费');
            }
            // 数据
            $data = [
                'user_id'        => $request->user->id,
                'order_no'       => $orderData->no,
                'amount'         => $apiAmount,
                'api_amount'     => $apiAmount,
                'api_deposit'    => $apiDeposit,
                'api_service'    => $apiService,
                'deposit'        => $apiDeposit,
                'consult'        => 2, // 接单发起撤销
                'revoke_message' => $content,
            ];
            // 更新协商信息到协商表
            LevelingConsult::updateOrCreate(['order_no' => $orderData->no], $data);
            // 调用工厂模式协商操作
            DailianFactory::choose('revoke')->run($orderData->no, $request->user->id, false);
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->partner(0, $e->getMessage());
        }  catch (Exception $e) {
            DB::rollBack();
            return response()->partner(0, '接口异常');
        }
        DB::commit();
        return response()->partner(1, '成功');
    }

    /**
     * 取消撤销
     * @param Request $request
     */
    public function cancelRevoke(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);
            // 会变成锁定
            DailianFactory::choose('cancelRevoke')->run($orderData->no, $request->user->id, false);
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        } 
        return response()->partner(1, '成功');
    }

    /**
     * 不同意撤销
     * @param Request $request
     */
    public function refuseRevoke(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);
            // 会变成锁定
            DailianFactory::choose('cancelRevoke')->run($orderData->no, $request->user->id, false);
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
        return response()->partner(1, '成功');
    }

    /**
     * 同意撤销
     * @param Request $request
     */
    public function agreeRevoke(Request $request)
    {
        DB::beginTransaction();
        try {
            $apiService = $request->input('api_service', '');

            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            if (! is_numeric($apiService)) {
                return response()->partner(0, '回传手续费必须是数字');
            }

            if ($apiService < 0) {
                return response()->partner(0, '回传手续费必须大于或等于0');
            }

            $data = [
                'api_service' => $apiService,
                'complete' => 1, // 撤销完成
            ];

            LevelingConsult::updateOrCreate(['order_no' => $orderData->no], $data);

            DailianFactory::choose('agreeRevoke')->run($orderData->no, $request->user->id, false);
            // 手续费写到order_detail中
            OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $orderData->no)
                ->update(['field_value' => $apiService]);

        } catch (DailianException $e) {
            DB::rollBack();
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return response()->partner(0, '接口异常');
        }
        DB::commit();
        return response()->partner(1, '成功');
    }

    /**
     * 强制撤销
     * @param Request $request
     */
    public function forceRevoke(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('forceRevoke')->run($orderData->no, $request->user->id);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * 申请仲裁
     * @param Request $request
     */
    public function applyArbitration(Request $request)
    {
        DB::beginTransaction();
        try {
            $content = $request->input('content', '无');

            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            $data = [
                'user_id' => $request->user->id,
                'complain' => 2,
                'complain_message' => $content,
            ];
            LevelingConsult::updateOrCreate(['order_no' => $orderData->no], $data);

            DailianFactory::choose('applyArbitration')->run($orderData->no, $request->user->id, false);
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->partner(0, $e->getMessage());
        }  catch (Exception $e) {
            DB::rollBack();
            return response()->partner(0, '接口异常');
        }
        DB::commit();
        return response()->partner(1, '成功');
    }

    /**
     * 取消仲裁
     * @param Request $request
     */
    public function cancelArbitration(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('cancelArbitration')->run($orderData->no, $request->user->id, false);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * 客服仲裁(强制仲裁)
     * @param Request $request
     */
    public function forceArbitration(Request $request)
    {
        DB::beginTransaction();
        try {
            $apiAmount = $request->input('api_amount', ''); // 回传代练费
            $apiDeposit = $request->input('api_deposit', ''); // 回传的双金
            $apiService = $request->input('api_service', ''); // 回传的手续费

            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            if (! is_numeric($apiDeposit) || ! is_numeric($apiService) || ! is_numeric($apiAmount)) {
                throw new DailianException('回传双金、手续费和代练费必须是数字');
            }

            if ($apiDeposit < 0 || $apiService < 0 || $apiAmount < 0) {
                throw new DailianException('回传双金、手续费和代练费必须大于等于0');
            }

            $data = [
                'api_amount' => $apiAmount,
                'api_deposit' => $apiDeposit,
                'api_service' => $apiService,
                'complete' => 2,
            ];
            // 更新代练协商申诉表
            LevelingConsult::updateOrCreate(['order_no' => $orderData->no], $data);
            // 同意申诉
            DailianFactory::choose('arbitration')->run($orderData->no, $request->user->id, 0);
            // 手续费写到order_detail中
            OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $orderData->no)
                ->update(['field_value' => $apiService]);

        } catch (DailianException $e) {
            DB::rollBack();
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
        DB::commit();
        return response()->partner(1, '成功');
    }

    /**
     * 异常
     * @param Request $request
     */
    public function abnormal(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('abnormal')->run($orderData->no, $request->user->id, false);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * 取消异常
     * @param Request $request
     */
    public function cancelAbnormal(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('cancelAbnormal')->run($orderData->no, $request->user->id, false);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * 
     * @param  Request  $request [description]
     * @return function          [description]
     */
    public function callback(Request $request) 
    {
        try {
            $order = OrderModel::where('no', $request->input('no', ''))->first();

            if (! $order) {
                 return response()->partner(0, '我方订单号缺失或错误');
            }

            if (! $request->input('order_no', '')) {
                return response()->partner(0, '您的代练平台订单号缺失');
            }

            $third = config('leveling.third')[$request->user->id];

            // 更新订单详情表数据
            OrderDetail::where('order_no', $order->no)
                ->where('field_name', config('leveling.third_orders')[$third])
                ->update(['field_value' => $request->order_no]);

            myLog('order.operate.mayi-callback', ['order_no' => $order->no, 'mayi_order_no' => $request->order_no, 'time' => Carbon::now()->toDateTimeString()]);
            return response()->partner(1, '成功');
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * 完成操作
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function complete(Request $request)
    {
        try {
            $orderData = $this->getOrderAndOrderDetails($request->order_no);

            DailianFactory::choose('complete')->run($orderData->no, $request->user->id, false);

            return response()->partner(1, '成功');
        } catch (DailianException $e) {
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            return response()->partner(0, '接口异常');
        }
    }

    /**
     * @param Request $request
     */
    public function newMessage(Request $request)
    {
        try {
            $thirdOrderNo = $request->order_no;
            $contents = $request->contents;
            $date = $request->date;

            LevelingMessage::create([
                'user_id' => 1, // 第三方平台在我们平台的ID
                'third' => 1,
                'third_order_no' => 1, // 第三方平台单号
                'foreign_order_no' => 1, // 天猫单号
                'order_no' => 1, // 我们平台单号
                'date' => 1, // 第三方平台单号留言时间
                'contents' => 1, // 第三方平台单号留言内容
            ]);
        } catch (\Exception $exception) {
            return response()->partner(0, '接收失败');
        }
        return response()->partner(1, '接收成功');
    }

}
