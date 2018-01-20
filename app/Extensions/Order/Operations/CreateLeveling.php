<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\CustomException;
use App\Exceptions\AssetException as Exception;
use App\Models\Game;
use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Api\GoodsRepository;
use App\Repositories\Backend\GameRepository;
use Asset, Auth, DB;
use App\Extensions\Asset\Expend;
use App\Services\Show91;
use App\Models\ThirdGame;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use App\Exceptions\DailianException;

/**
 * 创建代练订单
 * Class CreateLeveling
 * @package App\Extensions\Order\Operations
 */
class CreateLeveling extends \App\Extensions\Order\Operations\Base\Operation
{
    /**
     * @var int
     */
    protected $handledStatus = 1;

    /**
     * 模版ID
     * @var
     */
    protected $templateId;

    /**
     * @var
     */
    protected $game;

    /**
     * @var int
     */
    protected $type = 1;

    /**
     * @var string
     */
    protected $foreignOrderNO;

    /**
     * @var int
     */
    protected $source = 1;

    /**
     * 原单价
     * @var float|int
     */
    protected $originalPrice = 0;

    /**
     * @var array
     */
    protected $details;

    /**
     * 商品单价
     * @var float
     */
    protected $price = 0;

    /**
     * 备注
     * @var string
     */
    protected $remark;

    /**
     * @param int $gameId 游戏ID
     * @param int $templateId 模版ID
     * @param int $userId 用户id
     * @param string $foreignOrderNO 外部单号
     * @param float $price 发单价
     * @param float $originalPrice 原价
     * @param array $details 订单详细参数 例：['version' => '版本','account' => '账号','region'  => '区服']
     * @param string $remark  订单备注
     */
    public function __construct($gameId, $templateId, $userId, $foreignOrderNO, $price, $originalPrice, $details, $remark = '')
    {
        $this->userId = $userId;
        $this->foreignOrderNO = $foreignOrderNO;
        $this->originalPrice = $originalPrice? : 0;
        $this->details = $details;
        $this->remark = $remark;
        $this->price = $price;
        $this->templateId = $templateId;
        $this->game = Game::find($gameId);
        $this->runAfter = 1;
    }

    // 获取订单
    public function getObject()
    {
        $this->order = new Order;
    }

    public function setAttributes()
    {
        $this->order->no = generateOrderNo();
        $this->order->foreign_order_no = $this->foreignOrderNO;
        $this->order->source = 1;
        $this->order->goods_id = 0; // 商品ID无
        $this->order->goods_name = ''; // 商品名为下单标题
        $this->order->service_id = 2;// 服务类型;
        $this->order->service_name =  '游戏代练'; // 服务名;
        $this->order->game_id = $this->game->id; // 游戏ID
        $this->order->game_name = $this->game->name; // 游戏名
        $this->order->original_price = $this->originalPrice; // 原价
        $this->order->price = $this->price; // 发单价
        $this->order->quantity = 1; // 数量
        $this->order->original_amount =  $this->originalPrice;
        $this->order->amount = $this->price;
        $this->order->creator_user_id = $this->userId;
        $this->order->creator_primary_user_id = Auth::user()->getPrimaryUserId();
        $this->order->remark = $this->remark;

        // 记录订单详情
        if (!empty($this->details)) {
            $widget = GoodsTemplateWidget::where('goods_template_id', $this->templateId)->pluck('field_display_name', 'field_name');

            foreach ($widget as $k => $v) {

                $orderDetail = new OrderDetail;
                $orderDetail->order_no = $this->order->no;
                $orderDetail->field_name = $k;
                $orderDetail->field_display_name = $v;
                $orderDetail->field_value = $this->details[$k] ?? '';
                $orderDetail->creator_primary_user_id = $this->order->creator_primary_user_id;

                if (!$orderDetail->save()) {
                    throw new Exception('详情记录失败');
                }
            }

        }
    }

    public function updateAsset()
    {
        try {
            Asset::handle(new Expend($this->order->amount, 6, $this->order->no, '代练支出', $this->order->creator_primary_user_id));
        } catch (CustomException $customException) {
            throw new CustomException('代练价格不能大于账户余额');
        }

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new CustomException('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('申请失败');
        }
    }

    // 设置描述
    public function setDescription()
    {
        $sourceName = config('order.source')[$this->source];
        $this->description = "用户[{$this->userId}]从[{$sourceName}]渠道创建了订单";

        if ($this->order->status == 1) {
            $this->description .= "并付款";
        }
    }

    /**
     * 下单调外部接口
     * @throws CustomException
     */
    public function after()
    {
        if ($this->runAfter) {
            DB::beginTransaction();
            try {
                $result = Show91::addOrder($this->order);

                $thirdOrderNo = $result['data'];
                //将第三方订单号更新到order_detail中
                OrderDetail::where('order_no', $this->order->no)->where('field_name', 'third_order_no')->update([
                    'field_value' => $thirdOrderNo,
                ]);
                OrderDetail::where('order_no', $this->order->no)->where('field_name', 'third')->update([
                    'field_value' => 1, //91代练
                ]);

            } catch (CustomException $e) {
                throw new CustomException($e->getMessage());
                DB::rollBack();
            } catch (DailianException $dailian) {
                DB::rollBack();
                throw new DailianException($dailian->getMessage());
            }
            DB::commit();
        }
    }
}
