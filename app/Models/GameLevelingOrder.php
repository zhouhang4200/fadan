<?php

namespace App\Models;

use DB;
use Auth;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Services\RedisConnect;
use Illuminate\Database\Eloquent\Model;

class GameLevelingOrder extends Model
{
    public $fillable = [
        'trade_no',
        'status',
        'channel_order_trade_no',
        'channel_order_status',
        'platform_id',
        'platform_trade_no',
        'game_id',
        'repeat',
        'amount',
        'source_price',
        'security_deposit',
        'efficiency_deposit',
        'poundage',
        'source',
        'user_id',
        'parent_user_id',
        'take_user_id',
        'take_parent_user_id',
        'top',
        'game_region_id',
        'game_server_id',
        'game_leveling_type_id',
        'day',
        'hour',
        'title',
        'game_account',
        'game_password',
        'game_role',
        'customer_service_name',
        'seller_nick',
        'buyer_nick',
        'pre_sale',
        'take_order_password',
        'price_increase_step',
        'price_ceiling',
        'take_at',
        'top_at',
        'apply_complete_at',
        'complete_at',
        'created_at',
        'updated_at',
    ];

    /**
     * 一对一，订单详情
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gameLevelingOrderDetail()
    {
        return $this->hasOne(GameLevelingOrderDetail::class, 'game_leveling_order_trade_no', 'trade_no');
    }

    /**
     * 多对多，渠道表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameLevelingOrderRelationChannels()
    {
        return $this->hasMany(GameLevelingOrderRelationChannel::class, 'game_leveling_order_trade_no', 'trade_no');
    }

    /**
     * 一对多，前一个状态
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameLevelingOrderPreviousStatuses()
    {
        return $this->hasMany(GameLevelingOrderPreviousStatus::class, 'game_leveling_order_trade_no', 'trade_no');
    }

    /**
     * 订单前一个状态
     * @return mixed
     */
    public function previousStatus()
    {
        return $this->gameLevelingOrderPreviousStatuses()->latest('id')->value('status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gameLevelingOrderConsult()
    {
        return $this->hasOne(GameLevelingOrderConsult::class, 'game_leveling_order_trade_no', 'trade_no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gameLevelingOrderComplain()
    {
        return $this->hasOne(GameLevelingOrderComplain::class, 'game_leveling_order_trade_no', 'trade_no');
    }

    /**
     * 下单
     * @param User $user
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public static function placeOrder(User $user, $data = [])
    {
        DB::beginTransaction();
        try {
            $game = Game::find($data['game_id']);
            $region = GameRegion::find($data['game_region_id']);
            $server = GameServer::find($data['game_server_id']);
            $gameLevelingType = GameLevelingType::find($data['game_leveling_type_id']);
            $parent = $user->getPrimaryInfo();

            // 来源淘宝订单是否存在
            $sourceOrderNo = '';
            $sourcePrice = 0;
            $source = 1; // 人工
            $taobaoStatus = '';
            /****************存在来源单号则订单的来源单号和来源价格为淘宝相应单号与价格***********************/
            if (isset($data['channel_order_trade_no']) && ! empty($data['channel_order_trade_no'])) {
                $source = 2; // 淘宝
                $sourceOrderNo = $data['channel_order_trade_no'];
                $taobaoTrade = TaobaoTrade::where('tid', $sourceOrderNo)->first();
                $sourcePrice = $taobaoTrade->payment;

                /*************查询是否有进行中的淘宝单号*******************/
                $existOrder = GameLevelingOrder::where('channel_order_trade_no', $sourceOrderNo)->first();

                if ($existOrder && ! in_array($existOrder->status, [15, 16, 19, 20, 21, 22, 23, 24])) {
                    throw new Exception('该订单已经发布，请勿重发');
                } else if ($taobaoTrade) {
                    $taobaoTrade->handle_status = 1;
                    $taobaoTrade->save();
                    $taobaoStatus = 1;
                }
            } else {
                $sourcePrice = $data['source_amount'];
            }

            /*****创建订单*******/
            $orderData = [
                'trade_no' => generateOrderNo(),
                'status' => 1,
                'channel_order_status' => $taobaoStatus,
                'channel_order_trade_no' => $sourceOrderNo,
                'platform_id' => '',
                'platform_trade_no' => '',
                'game_id' => $game->id,
                'user_id' => $user->id,
                'parent_user_id' => $parent->id,
                'take_user_id' => 0,
                'take_parent_user_id' => 0,
                'amount' => $data['amount'],
                'source_amount' => $sourcePrice,
                'security_deposit' => $data['security_deposit'],
                'efficiency_deposit' => $data['efficiency_deposit'],
                'source' => $source,
                'top' => 0,
                'poundage' => 0,
                'game_region_id' => $region->id,
                'game_server_id' => $server->id,
                'game_leveling_type_id' => $gameLevelingType->id,
                'title' => $data['title'],
                'day' => $data['day'],
                'hour' => $data['hour'],
                'game_account' => $data['game_account'],
                'game_password' => $data['game_password'],
                'game_role' => $data['game_role'],
                'customer_service_name' => $user->username ?? '',
                'seller_nick' => $data['seller_nick'] ?? '',
                'buyer_nick' => $data['buyer_nick'] ?? '',
                'price_increase_step' => $data['price_increase_step'] ?? '',
                'price_ceiling' => $data['price_ceiling'] ?? '',
                'take_order_password' => '',
                'pre_sale' => '', // 接单客服
                'take_at' => null,
                'apply_complete_at' => null,
                'complete_at' => null,
                'top_at' => null,
            ];

            $order = static::create($orderData);

            /***存到订单详情表***/
            $details = [
                'game_leveling_order_trade_no' => $order->trade_no,
                'game_region_name' => $region->name,
                'game_server_name' => $server->name,
                'game_leveling_type_name' => $gameLevelingType->name,
                'game_name' => $game->name,
                'username' => $user->username,
                'parent_username' => $parent->username,
                'take_username' => '',
                'take_parent_username' => '',
                'user_phone' => $data['user_phone'] ?? '',
                'user_qq' => $data['user_qq'] ?? '',
                'player_name' => '',
                'player_phone' => $data['player_phone'] ?? '',
                'player_qq' => '',
                'parent_user_phone' => $parent->phone ?? '',
                'parent_user_qq' => $parent->qq ?? '',
                'take_user_qq' => '',
                'take_user_phone' => '',
                'take_parent_phone' => '',
                'take_parent_qq' => '',
                'explain' => $data['explain'] ?? '',
                'requirement' => $data['requirement'] ?? '',
            ];

            $gameLevelingOrderDetail = GameLevelingOrderDetail::create($details);

            /***存在来源订单号（淘宝主订单号）, 写入关联淘宝订单表***/
            // TODO 需要重写
//            static::changeSameOriginOrderSourcePrice($order, $data);

            /************写入订单操作记录***********/
            GameLevelingOrderLog::createOrderHistory($order, 1, "用户[{$order->user_id}]从[".config('order.source')[$order->source]."]渠道创建了订单");

            /************* 如果设置了接单人则直接变为接单*************************/
            if (isset($data['gainer_user_id']) && $data['gainer_user_id']) {
                $takeUser = User::find($data['gainer_user_id']);
                $takeParentUser = $takeUser->getPrimaryInfo();

                $order->status = 13;
                $order->take_user_id = $takeUser->id;
                $order->take_parent_user_id = $takeParentUser->id;
                $order->take_at = date('Y-m-d H:i:s');
                $order->save();

                $gameLevelingOrderDetail->take_username = $takeUser->username ?? '';
                $gameLevelingOrderDetail->take_user_qq = $takeUser->qq ?? '';
                $gameLevelingOrderDetail->take_user_phone = $takeUser->phone ?? '';
                $gameLevelingOrderDetail->take_parent_username = $takeParentUser->username ?? '';
                $gameLevelingOrderDetail->take_parent_phone = $takeParentUser->phone ?? '';
                $gameLevelingOrderDetail->take_parent_qq = $takeParentUser->qq ?? '';
                $gameLevelingOrderDetail->save();

                return $order;
            }

            /************加到redis发单队列************/
            $redis = RedisConnect::order();

            $sendOrder = [
                'order_no' => $order->trade_no,
                'game_name' => $gameLevelingOrderDetail->game_name,
                'game_region' => $gameLevelingOrderDetail->game_region_name,
                'game_serve' => $gameLevelingOrderDetail->game_server_name,
                'game_role' => $order->game_role,
                'game_account' => $order->game_account,
                'game_password' => $order->game_password,
                'game_leveling_type' => $gameLevelingOrderDetail->game_leveling_type_name,
                'game_leveling_title' => $order->title,
                'game_leveling_price' => $order->amount,
                'game_leveling_day' => $order->day,
                'game_leveling_hour' => $order->hour,
                'game_leveling_security_deposit' => $order->security_deposit,
                'game_leveling_efficiency_deposit' => $order->efficiency_deposit,
                'game_leveling_requirements' => $gameLevelingOrderDetail->requirement,
                'game_leveling_instructions' => $gameLevelingOrderDetail->explain,
                'businessman_phone' => $gameLevelingOrderDetail->user_phone,
                'businessman_qq' => $gameLevelingOrderDetail->user_qq,
                'order_password' => $order->take_order_password,
                'creator_username' => $gameLevelingOrderDetail->username,
            ];
            $redis->lpush('new-order:send', json_encode($sendOrder));
        } catch (Exception $e) {
            DB::rollback();
            myLog('place-order-error', ['trade_no' => $order->trade_no ?? '', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('服务器异常，下单失败！' . $e->getMessage());
        }
        DB::commit();
        return $order;
    }

    /**
     * 设置了自动加价,则开启加价
     * @param $order
     */
    public static function checkAutoMarkUpPrice($order)
    {
        // 设置了自动加价,则开启加价
        if (! isset($order->price_increase_step) || empty($order->price_increase_step)
            || ! isset($order->price_ceiling) || empty($order->price_ceiling)) {
            Redis::hDel('order:price-markup', $order->trade_no); // 没有设置或设置取消了，清除redis
        } elseif (isset($order->price_increase_step) && ! empty($order->price_increase_step)
            && isset($order->price_ceiling) && ! empty($order->price_ceiling)) {
            if (bcsub($order->amount, $order->price_ceiling) >= 0) {
                Redis::hDel('order:price-markup', $order->trade_no); // 设置的最大加价金额小于代练金额，清除redis
            } else {
                $key = $order->trade_no;
                $name = "order:price-markup";
                $value = "0@".$order->amount."@".$order->updated_at;

                Redis::hSet($name, $key, $value);
            }
        }
    }

    /**
     * 更新相同淘宝单号的所有订单来源价格
     * @param $order
     */
    public static function changeSameOriginOrderSourcePrice(GameLevelingOrder $order, $data = [])
    {
        if ($order->channel_order_trade_no) {
            $gameLevelingOrderRelationChannel = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('game_leveling_channel_order_trade_no', $order->channel_order_trade_no)
                ->first();

            if (!$gameLevelingOrderRelationChannel) {
                // 将淘宝主订单号写入关联的淘宝订单表
                GameLevelingOrderRelationChannel::create([
                    'channel' => 1,
                    'game_leveling_order_trade_no' => $order->trade_no,
                    'game_leveling_channel_order_trade_no' => $order->channel_order_trade_no,
                    'payment' => $order->source_price,
                ]);
            }

            // 是否存在不同的补款单号1
            if (isset($data['source_order_no_1']) && ! empty($data['source_order_no_1']) && $data['source_order_no_1'] != $order->source_order_no) {
                $payment1 = TaobaoTrade::where('tid', $data['source_order_no_1'])->value('payment');

                $gameLevelingOrderRelationChannel1 = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                    ->where('game_leveling_channel_order_trade_no', $data['source_order_no_1'])
                    ->first();

                if (!$gameLevelingOrderRelationChannel1) {
                    GameLevelingOrderRelationChannel::create([
                        'channel' => 1,
                        'game_leveling_order_trade_no' => $order->trade_no,
                        'game_leveling_channel_order_trade_no' => $data['source_order_no_1'],
                        'payment' => $payment1,
                    ]);
                }
            }
            // 是否存在不同的补款单号2
            if (isset($data['source_order_no_2']) && ! empty($data['source_order_no_2']) && $data['source_order_no_2'] != $order->source_order_no) {
                $payment2 = TaobaoTrade::where('tid', $data['source_order_no_2'])->value('payment');

                $gameLevelingOrderRelationChannel2 = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                    ->where('game_leveling_channel_order_trade_no', $data['source_order_no_1'])
                    ->first();

                if (!$gameLevelingOrderRelationChannel2) {
                    GameLevelingOrderRelationChannel::create([
                        'channel' => 1,
                        'game_leveling_order_trade_no' => $order->trade_no,
                        'game_leveling_channel_order_trade_no' => $data['source_order_no_2'],
                        'payment' => $payment2,
                    ]);
                }
            }

            /*****更新订单的来源价格*********/
            $totalSourcePrice = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)->sum('payment');

            if ($totalSourcePrice != $order->source_price) {
                $order->source_price = $totalSourcePrice;
                $order->save();
            }

            /********更新相同淘宝单号的所有订单来源价格*********/
            $otherGameLevelingOrders = GameLevelingOrderRelationChannel::where('game_leveling_channel_order_trade_no', $order->channel_order_trade_no)
                ->where('game_leveling_order_trade_no', '!=', $order->trade_no)
                ->pluck('game_leveling_order_trade_no')
                ->unique();

            if ($otherGameLevelingOrders->count() > 1) {
                foreach($otherGameLevelingOrders as $otherGameLevelingOrder) {
                    $otherGameLevelingOrder->source_price = $totalSourcePrice;
                    // 关联表的订单也随着修改或新增
                    if (isset($data['source_order_no_1']) && ! empty($data['source_order_no_1']) && $data['source_order_no_1'] != $order->source_order_no) {
                        $issetRecord1 = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                            ->where('game_leveling_channel_order_trade_no', $data['source_order_no_1'])
                            ->first();

                        GameLevelingOrderRelationChannel::updateOrCreate([
                            'game_leveling_order_trade_no' => $otherGameLevelingOrder->trade_no,
                            'game_leveling_channel_order_trade_no' =>  $data['source_order_no_1'],
                        ],[
                            'channel' => 1,
                            'game_leveling_order_trade_no' => $otherGameLevelingOrder->trade_no,
                            'game_leveling_channel_order_trade_no' =>  $data['source_order_no_1'],
                            'payment' => $issetRecord1->payment,
                        ]);
                    }
                    // 关联表的订单也随着修改或新增
                    if (isset($data['source_order_no_2']) && ! empty($data['source_order_no_2']) && $data['source_order_no_2'] != $order->source_order_no) {
                        $issetRecord2 = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                            ->where('game_leveling_channel_order_trade_no', $data['source_order_no_2'])
                            ->first();

                        GameLevelingOrderRelationChannel::updateOrCreate([
                            'game_leveling_order_trade_no' => $otherGameLevelingOrder->trade_no,
                            'game_leveling_channel_order_trade_no' =>  $data['source_order_no_2'],
                        ],[
                            'channel' => 1,
                            'game_leveling_order_trade_no' => $otherGameLevelingOrder->trade_no,
                            'game_leveling_channel_order_trade_no' =>  $data['source_order_no_2'],
                            'payment' => $issetRecord2->payment,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 按传入条件过滤
     * @param $query
     * @param $conditions
     * @return mixed
     */
    public static function scopeFilter($query, $conditions)
    {

        if (isset($conditions['order_no']) && $conditions['order_no']) {
            $query->where('trade_no', $conditions['order_no'])
                ->orWhere('platform_trade_no', $conditions['order_no'])
                ->orWhere('channel_order_trade_no', $conditions['order_no']);
        }
        if (isset($conditions['trade_no']) && $conditions['trade_no']) {
            $query->where('trade_no', $conditions['trade_no']);
        }
        if (isset($conditions['buyer_nick']) && $conditions['buyer_nick']) {
//            $query->where('buyer_nick', $conditions['buyer_nick']);
        }
        if (isset($conditions['parent_user_id']) && $conditions['parent_user_id']) {
            $query->where('parent_user_id', $conditions['parent_user_id']);
        }
        if (isset($conditions['user_id']) && $conditions['user_id']) {
            $query->where('user_id', $conditions['user_id']);
        }
        if (isset($conditions['game_id']) && $conditions['game_id']) {
            $query->where('game_id', $conditions['game_id']);
        }
        if (isset($conditions['game_leveling_type_id']) && $conditions['game_leveling_type_id']) {
            $query->where('game_leveling_type_id', $conditions['game_leveling_type_id']);
        }
        if (isset($conditions['platform_id']) && $conditions['platform_id']) {
            $query->where('platform_id', $conditions['platform_id']);
        }
        if (isset($conditions['status']) && $conditions['status']) {
            $query->where('status', $conditions['status']);
        }
        if (isset($conditions['created_at']) && $conditions['created_at']) {
            $query->whereBetween('created_at', $conditions['created_at']);
        }
        return $query;
    }

    /**
     * 支出金额
     * @return int|mixed|string
     */
    public function payAmount()
    {
        // 发单
        if (request()->user()->getPrimaryUserId() == $this->parent_user_id) {
            if ($this->status == 19) {
                return $this->gameLevelingOrderConsult->amount + 0;
            } elseif ($this->status == 20) {
                return $this->amount + 0;
            } elseif ($this->status == 21) {
                return $this->gameLevelingOrderComplain->amount + 0;
            } else {
                return 0;
            }
        } elseif (request()->user()->getPrimaryUserId() == $this->take_parent_user_id) { // 接单
            if ($this->status == 19) {
                return bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit) + 0;
            } elseif ($this->status == 21) {
                return bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit) + 0;
            } else {
                return 0;
            }
        }
    }

    /**
     * 获得金额
     * @return int|mixed|string
     */
    public function getAmount()
    {
        // 发单
        if (request()->user()->getPrimaryUserId() == $this->parent_user_id) {
            if ($this->status == 19) {
                return bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit) + 0;
            } elseif ($this->status == 21) {
                return bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit) + 0;
            } else {
                return 0;
            }
        } elseif (request()->user()->getPrimaryUserId() == $this->take_parent_user_id) { // 接单
            if ($this->status == 19) {
                return $this->gameLevelingOrderConsult->amount + 0;
            } elseif ($this->status == 20) {
                return $this->amount;
            } elseif ($this->status == 21) {
                return $this->gameLevelingOrderComplain->amount + 0;
            } else {
                return 0;
            }
        }
    }

    /**
     * 获取订单手续费
     * @return int|mixed
     */
    public function getPoundage()
    {
        if ($this->status == 19) {
            return $this->gameLevelingOrderConsult->poundage + 0;
        } elseif ($this->status == 21) {
            return $this->gameLevelingOrderComplain->poundage + 0;
        } else {
            return 0;
        }
    }

    /**
     * 获取订单利润
     * @return int|string
     */
    public function getProfit()
    {
        return $this->getAmount() - $this->payAmount() - $this->getPoundage() + $this->complainAmount() + 0;
    }

    /**
     * 获取撤销发起人
     * @return int 0 不存在撤销 1 撤销发起人为 发单方
     */
    public function getConsultInitiator()
    {
        return (int) optional($this->gameLevelingOrderConsult)->initiator;
    }

    /**
     * 获取仲裁发起人
     * @return int 0 不存在仲裁 1 仲裁发起人为 发单方
     */
    public function getComplainInitiator()
    {
        return (int) optional($this->gameLevelingOrderComplain)->initiator;
    }

    /**
     * 剩余时间
     */
    public function leftTime()
    {
        // 如果存在接单时间
        if (!empty($this->take_at)) {
            // 计算到期的时间戳
            $expirationTimestamp = strtotime($this->take_at) + $this->day * 86400 + $this->hour * 3600;
            // 计算剩余时间
            $leftSecond = $expirationTimestamp - time();
            return Sec2Time($leftSecond); // 剩余时间
        } else {
            return '';
        }
    }

    /**
     * 最终支付金额
     * @return int|string
     */
    public function complainAmount()
    {
        $complaintAmount = 0;
        $complaint = BusinessmanComplaint::where('order_no', $this->trade_no)->first();
        if (isset($complaint) && ! empty($complaint)) {
            if ($complaint->complaint_primary_user_id == $this->creator_primary_user_id) {
                $complaintAmount = $complaint->amount+0;
            } elseif ($complaint->be_complaint_primary_user_id == $this->creator_primary_user_id) {
                $complaintAmount = bcmul(-1, $complaint->amount)+0;
            }
        }
        return $complaintAmount;
    }

    /**
     * 获取订单撤销 描述
     * @return string
     */
    public function getConsultDescribe()
    {
        if (! is_null($this->gameLevelingOrderConsult) && optional($this->gameLevelingOrderConsult)->status != 3) {

            if ($this->gameLevelingOrderConsult->initiator == 1) { // 如果发起人为发单方

                // 当前用户父Id 等于撤销发起人
                if ($this->gameLevelingOrderConsult->parent_user_id == request()->user()->parent_id) {
                    return sprintf("您发起撤销, <br/> 你支付代练费用 %.2f 元, 对方支付保证金 %.2f, <br/> 原因: %s",
                        $this->gameLevelingOrderConsult->amount,
                        bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit),
                        $this->gameLevelingOrderConsult->reason
                    );
                } else {
                    return sprintf("对方发起撤销, <br/> 对方支付代练费用 %.2f 元, 你方支付保证金 %.2f, <br/> 原因: %s",
                        $this->gameLevelingOrderConsult->amount,
                        bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit),
                        $this->gameLevelingOrderConsult->reason
                    );
                }
            } else if ($this->gameLevelingOrderConsult->initiator == 2) {  // 如果发起人为接单方

                if ($this->gameLevelingOrderConsult->parent_user_id == request()->user()->parent_id) {
                    return sprintf("您发起撤销, <br/> 对方支付代练费用 %.2f 元, 你支付保证金 %.2f, <br/> 原因: %s",
                        $this->gameLevelingOrderConsult->amount,
                        bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit),
                        $this->gameLevelingOrderConsult->reason
                    );
                } else {
                    return sprintf("对方发起撤销, <br/> 对方支付代练费用 %.2f 元, 您支付保证金 %.2f, <br/> 原因: %s",
                        $this->gameLevelingOrderConsult->amount,
                        bcadd($this->gameLevelingOrderConsult->security_deposit, $this->gameLevelingOrderConsult->efficiency_deposit),
                        $this->gameLevelingOrderConsult->reason
                    );
                }
            }

        } else {
            return '';
        }
    }

    /**
     * 获取订单仲裁 描述
     * @return string
     */
    public function getComplainDescribe()
    {
        if (! is_null($this->gameLevelingOrderComplain) && $this->gameLevelingOrderComplain->status != 3) {
            // 当前用户父Id 等于仲裁发起人
            if ($this->gameLevelingOrderComplain->parent_user_id == request()->user()->parent_id) {
                return sprintf("你发起仲裁 <br/> 原因: %s",
                    $this->gameLevelingOrderComplain->reason
                );
            } else {
                return sprintf("对方发起仲裁 <br/> 原因: %s",
                    $this->gameLevelingOrderComplain->reason
                );
            }
        } else {
            return '';
        }
    }

    /**
     * 仲裁结果
     * @return string
     */
    public function getComplainResult()
    {
        if (! is_null($this->gameLevelingOrderComplain) && $this->gameLevelingOrderComplain->status == 2) {

            if ($this->gameLevelingOrderComplain->initiator == 1) { // 如果发起人为发单方

                // 当前用户父Id 等于仲裁发起人
                if ($this->gameLevelingOrderComplain->parent_user_id == request()->user()->parent_id) {
                    return sprintf("客服进行了【仲裁】  <br/> 你支付代练费用 %.2f 元, 对方支付保证金 %.2f <br/> 仲裁说明： %s",
                        $this->gameLevelingOrderComplain->amount,
                        bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit),
                        $this->gameLevelingOrderComplain->reason
                    );
                } else {

                    return sprintf("客服进行了【仲裁】  <br/> 你支付代练费用 %.2f 元, 对方支付保证金 %.2f <br/> 仲裁说明： %s",
                        $this->gameLevelingOrderComplain->amount,
                        bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit),
                        $this->gameLevelingOrderComplain->reason
                    );
                }
            } else if ($this->gameLevelingOrderComplain->initiator == 2) {  // 如果发起人为接单方
                // 客服进行了【仲裁】【你（对方）支出代练费1.0元，对方（你）支出保证金0.0元。仲裁说明：经查证，双方协商退单，已判定】
                if ($this->gameLevelingOrderComplain->parent_user_id == request()->user()->parent_id) {
                    return sprintf("客服进行了【仲裁】 <br/> 对方支付代练费用 %.2f 元, 你支付保证金 %.2f <br/> 仲裁说明： %s",
                        $this->gameLevelingOrderComplain->amount,
                        bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit),
                        $this->gameLevelingOrderComplain->reason
                    );
                } else {
                    return sprintf("客服进行了【仲裁】 <br/> 你支付代练费用 %.2f 元, 对方支付保证金 %.2f <br/> 仲裁说明： %s",
                        $this->gameLevelingOrderComplain->amount,
                        bcadd($this->gameLevelingOrderComplain->security_deposit, $this->gameLevelingOrderComplain->efficiency_deposit),
                        $this->gameLevelingOrderComplain->reason
                    );
                }
            }
        } else {
            return '';
        }
    }

    /**
     * 财务订单搜索
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public static function scopeFinanceOrderFilter($query, $filter = [])
    {
        if ($filter['tradeNo']) {
            $query->where('trade_no', $filter['tradeNo']);
        }

        if ($filter['gameId']) {
            $query->where('game_id', $filter['gameId']);
        }

        if ($filter['sellerNick']) {
            $query->where('seller_nick', $filter['sellerNick']);
        }

        if ($filter['platformId']) {
            $query->where('platform_id', $filter['platformId']);
        }

        if ($filter['status']) {
            $query->where('status', $filter['status']);
        }

        if ($filter['startDate']) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if ($filter['endDate']) {
            $query->where('created_at', '<=', $filter['endDate']);
        }

        return $query;
    }

    public function gameLevelingChannelOrder()
    {
        return $this->belongSto(GameLevelingChannelOrder::class, 'trade_no', 'channel_order_trade_no');
    }
}
