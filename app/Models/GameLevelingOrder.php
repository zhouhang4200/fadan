<?php

namespace App\Models;

use DB;
use Auth;
use Exception;
use App\Services\RedisConnect;
use Illuminate\Database\Eloquent\Model;

class GameLevelingOrder extends Model
{
    public $fillable = [
        'trade_no',
        'status',
        'platform_id',
        'platform_no',
        'game_id',
        'game_name',
        'amount',
        'security_deposit',
        'efficiency_deposit',
        'poundage',
        'get_amount',
        'user_id',
        'username',
        'parent_user_id',
        'parent_username',
        'take_user_id',
        'take_username',
        'take_parent_user_id',
        'take_parent_username',
        'order_type_id',
        'game_type_id',
        'game_class_id',
        'region_id',
        'region_name',
        'server_id',
        'server_name',
        'game_leveling_type_id',
        'game_leveling_type_name',
        'title',
        'day',
        'hour',
        'game_account',
        'game_password',
        'game_role',
        'user_phone',
        'user_qq',
        'customer_service_name',
        'seller_nick',
        'pre_sale',
        'explain',
        'requirement',
        'take_order_password',
        'player_name',
        'player_phone',
        'player_qq',
        'take_at',
        'price_increase_step',
        'price_ceiling',
        'apply_complete_at',
        'complete_at',
        'source',
        'top',
        'top_at',
        'parent_user_phone',
        'parent_user_qq',
        'take_user_qq',
        'take_user_phone',
        'take_parent_phone',
        'take_parent_qq',
        'created_at',
        'updated_at',
    ];

    /**
     * 一对一，订单详情
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gameLevelingOrderDetail()
    {
        return $this->hasOne(GameLevelingOrderDetail::class, 'trade_no', 'trade_no');
    }

    public function gameLevelingTaobaoTrades()
    {
        return $this->hasMany(GameLevelingTaobaoTrade::class, 'trade_no', 'taobao_trade_no');
    }

    /**
     * 下单
     * @param $data
     * @param $user
     * @return mixed
     */
    public static function placeOrder(User $user, $data = [])
    {
        DB::beginTransaction();
        try {
            $game = Game::find($data['game_id']);
            $region = Region::find($data['region']);
            $server = Server::find($data['serve']);
            $gameLevelingType = GameLevelingType::find($data['game_leveling_type']);
            $parent = $user->getPrimaryInfo();

            // 来源淘宝订单是否存在
            $sourceOrderNo = '';
            $sourcePrice = 0;
            $source = 1; // 人工
            /****************存在来源单号则订单的来源单号和来源价格为淘宝相应单号与价格***********************/
            if (isset($data['source_order_no']) && ! empty($data['source_order_no'])) {
                $source = 2; // 淘宝
                $sourceOrderNo = $data['source_order_no'];
                $taobaoTrade = TaobaoTrade::where('tid', $sourceOrderNo)->first();
                $sourcePrice = $taobaoTrade->payment;

                /*************查询是否有进行中的淘宝单号*******************/
                $existOrder = GameLevelingTaobaoTrade::where('taobao_trade_no', $sourceOrderNo)
                    ->first();

                if ($existOrder && ! in_array($existOrder->status, [15, 16, 19, 20, 21, 22, 23, 24])) {
                    throw new Exception('该订单已经发布，请勿重发');
                } else if ($taobaoTrade) {
                    $taobaoTrade->handle_status = 1;
                    $taobaoTrade->save();
                }
            }

            /*****创建订单*******/
            $data = [
                'trade_no' => generateOrderNo(),
                'status' => 1,
                'taobao_status' => 1,
                'source_order_no' => $sourceOrderNo,
                'platform_id' => 0,
                'platform_no' => '',
                'game_id' => $game->id,
                'user_id' => $user->id,
                'parent_user_id' => $parent->id,
                'take_user_id' => 0,
                'take_parent_user_id' => 0,
                'amount' => $data['game_leveling_amount'],
                'source_price' => $sourcePrice,
                'security_deposit' => $data['security_deposit'],
                'efficiency_deposit' => $data['efficiency_deposit'],
                'source' => $source,
                'top' => 0,
                'poundage' => 0,
                'region_id' => $region->id,
                'server_id' => $server->id,
                'game_leveling_type_id' => $gameLevelingType->id,
                'title' => $data['game_leveling_title'],
                'day' => $data['game_leveling_day'],
                'hour' => $data['game_leveling_hour'],
                'game_account' => $data['account'],
                'game_password' => $data['password'],
                'game_role' => $data['role'],
                'customer_service_name' => $user->username,
                'seller_nick' => $data['seller_nick'] ?? '',
                'buyer_nick' => $data['client_wang_wang'],
                'price_increase_step' => $data['markup_range'] ?? '',
                'price_ceiling' => $data['markup_top_limit'] ?? '',
                'take_order_password' => '',
                'pre_sale' => '', // 接单客服
                'take_at' => null,
                'apply_complete_at' => null,
                'complete_at' => null,
                'top_at' => null,
            ];

            $order = static::create($data);

            /***存到订单详情表***/
            $details = [
                'trade_no' => $order->trade_no,
                'region_name' => $region->name,
                'server_name' => $server->name,
                'game_leveling_type_name' => $gameLevelingType->name,
                'game_name' => $game->name,
                'username' => $user->username,
                'parent_username' => $parent->username,
                'take_username' => '',
                'take_parent_username' => '',
                'user_phone' => $data['client_phone'] ?? '',
                'user_qq' => $data['user_qq'] ?? '',
                'player_name' => '',
                'player_phone' => '',
                'player_qq' => '',
                'parent_user_phone' => $parent->phone ?? '',
                'parent_user_qq' => $parent->qq ?? '',
                'take_user_qq' => '',
                'take_user_phone' => '',
                'take_parent_phone' => '',
                'take_parent_qq' => '',
                'explain' => $data['game_leveling_instructions'] ?? '',
                'requirement' => $data['game_leveling_requirements'] ?? '',
            ];

            $gameLevelingOrderDetail = GameLevelingOrderDetail::create($details);

            /***存在来源订单号（淘宝主订单号）, 写入关联淘宝订单表***/
            if ($order->source_order_no) {
                // 将淘宝主订单号写入关联的淘宝订单表
                GameLevelingTaobaoTrade::create([
                    'trade_no' => $order->trade_no,
                    'taobao_trade_no' => $order->source_order_no,
                    'payment' => $sourcePrice,
                ]);
                // 是否存在不同的补款单号1
                if (isset($data['source_order_no_1']) && ! empty($data['source_order_no_1']) && $data['source_order_no_1'] != $order->source_order_no) {
                    $payment1 = TaobaoTrade::where('tid', $data['source_order_no_1'])->value('payment');
                    GameLevelingTaobaoTrade::create([
                        'trade_no' => $order->trade_no,
                        'taobao_trade_no' => $data['source_order_no_1'],
                        'payment' => $payment1,
                    ]);
                }
                // 是否存在不同的补款单号2
                if (isset($data['source_order_no_2']) && ! empty($data['source_order_no_2']) && $data['source_order_no_2'] != $order->source_order_no) {
                    $payment2 = TaobaoTrade::where('tid', $data['source_order_no_2'])->value('payment');
                    GameLevelingTaobaoTrade::create([
                        'trade_no' => $order->trade_no,
                        'taobao_trade_no' => $data['source_order_no_2'],
                        'payment' => $payment2,
                    ]);
                }

                /*****更新订单的来源价格*********/
                $totalSourcePrice = GameLevelingTaobaoTrade::where('trade_no', $order->trade_no)->sum('payment');

                if ($totalSourcePrice != $order->source_price) {
                    $order->source_price = $totalSourcePrice;
                    $order->save();
                }

                /********更新来源价格（相同淘宝单号）的所有订单*********/
                $otherGameLevelingOrders = GameLevelingTaobaoTrade::where('taobao_trade_no', $data['source_order_no'])
                    ->where('trade_no', '!=', $order->trade_no)
                    ->pluck('trade_no')
                    ->unique();

                if ($otherGameLevelingOrders->count() > 1) {
                    foreach($otherGameLevelingOrders as $otherGameLevelingOrder) {
                        $otherGameLevelingOrder->source_price = $totalSourcePrice;
                        // 关联表的订单也随着修改或新增
                        if (isset($data['source_order_no_1']) && ! empty($data['source_order_no_1']) && $data['source_order_no_1'] != $order->source_order_no) {
                            $issetRecord1 = GameLevelingTaobaoTrade::where('trade_no', $order->trade_no)
                                ->where('taobao_trade_no', $data['source_order_no_1'])
                                ->first();

                            GameLevelingTaobaoTrade::updateOrCreate([
                                'trade_no' => $otherGameLevelingOrder->trade_no,
                                'taobao_trade_no' =>  $data['source_order_no_1'],
                            ],[
                                'trade_no' => $otherGameLevelingOrder->trade_no,
                                'taobao_trade_no' =>  $data['source_order_no_1'],
                                'payment' => $issetRecord1->payment,
                            ]);
                        }
                        // 关联表的订单也随着修改或新增
                        if (isset($data['source_order_no_2']) && ! empty($data['source_order_no_2']) && $data['source_order_no_2'] != $order->source_order_no) {
                            $issetRecord2 = GameLevelingTaobaoTrade::where('trade_no', $order->trade_no)
                                ->where('taobao_trade_no', $data['source_order_no_2'])
                                ->first();

                            GameLevelingTaobaoTrade::updateOrCreate([
                                'trade_no' => $otherGameLevelingOrder->trade_no,
                                'taobao_trade_no' =>  $data['source_order_no_2'],
                            ],[
                                'trade_no' => $otherGameLevelingOrder->trade_no,
                                'taobao_trade_no' =>  $data['source_order_no_2'],
                                'payment' => $issetRecord2->payment,
                            ]);
                        }
                    }
                }
            }

            /************写入订单操作记录***********/
            $historyData = [
                'order_no' => $order->trade_no,
                'user_id' => $order->user_id,
                'admin_user_id' => '',
                'type' => 1,
                'name' => '创建',
                'description' => "用户[{$order->user_id}]从[".config('order.source')[$order->source]."]渠道创建了订单",
                'before' => '',
                'after' => '',
                'created_at' => Carbon::now()->toDateTimeString(),
                'creator_primary_user_id' => $order->parent_user_id,
            ];

            OrderHistory::create($historyData);

            /************加到redis发单队列************/
            $redis = RedisConnect::order();

            $sendOrder = [
                'order_no' => $order->trade_no,
                'game_name' => $order->game_name,
                'game_region' => $order->region_name,
                'game_serve' => $order->server_name,
                'game_role' => $order->game_role,
                'game_account' => $order->game_account,
                'game_password' => $order->game_password,
                'game_leveling_type' => $order->game_leveling_type_name,
                'game_leveling_title' => $order->title,
                'game_leveling_price' => $order->amount,
                'game_leveling_day' => $order->day,
                'game_leveling_hour' => $order->hour,
                'game_leveling_security_deposit' => $order->security_deposit,
                'game_leveling_efficiency_deposit' => $order->efficiency_deposit,
                'game_leveling_requirements' => $order->requirement,
                'game_leveling_instructions' => $order->explain,
                'businessman_phone' => $order->user_phone,
                'businessman_qq' => $order->user_qq,
                'order_password' => $order->game_password,
                'creator_username' => $order->username,
            ];
            $redis->lpush('order:send', json_encode($sendOrder));

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
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('服务器异常，下单失败！');
        }
        DB::commit();
        return $order;
    }
}
