<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
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

    public static function placeOrder($data = [])
    {
        $game = Game::find($data['game_id']);
        $region = Region::find($data['region']);
        $server = Server::find($data['serve']);
        $gameLevelingType = GameLevelingType::find($data['game_leveling_type']);
        $user = Auth::user();
        $parent = $user->getPrimaryInfo();

        $data = [
            'trade_no' => generateOrderNo(),
            'status' => 1,
            'platform_id' => 0,
            'platform_no' => '',
            'game_id' => $game->id,
            'game_name' => $game->name,
            'amount' => $data['game_leveling_amount'],
            'source_price' => $data['source_price'],
            'security_deposit' => $data['security_deposit'],
            'efficiency_deposit' => $data['efficiency_deposit'],
            'poundage' => 0,
            'get_amount' => 0,
            'user_id' => $user->id,
            'username' => $user->name,
            'parent_user_id' => $parent->id,
            'parent_username' => $parent->name,
            'take_user_id' => 0,
            'take_username' => '',
            'take_parent_user_id' => 0,
            'take_parent_username' => '',
            'order_type_id' => 1,
            'game_type_id' => 0,
            'game_class_id' => 0,
            'region_id' => $region->id,
            'region_name' => $region->name,
            'server_id' => $server->id,
            'server_name' => $server->name,
            'game_leveling_type_id' => $gameLevelingType->id,
            'game_leveling_type_name' => $gameLevelingType->name,
            'title' => $data['game_leveling_title'],
            'day' => $data['game_leveling_day'],
            'hour' => $data['game_leveling_hour'],
            'game_account' => $data['account'],
            'game_password' => $data['password'],
            'game_role' => $data['role'],
            'user_phone' => $data['client_phone'] ?? '',
            'user_qq' => $data['user_qq'] ?? '',
            'customer_service_name' => $user->name,
            'seller_nick' => $data['seller_nick'] ?? '',
            'buyer_nick' => $data['client_wang_wang'],
            'pre_sale' => '',
            'explain' => $data['game_leveling_instructions'] ?? '',
            'requirement' => $data['game_leveling_requirements'] ?? '',
            'take_order_password' => '',
            'player_name' => '',
            'player_phone' => '',
            'player_qq' => '',
            'take_at' => null,
            'price_increase_step' => $data['markup_range'] ?? '',
            'price_ceiling' => $data['markup_top_limit'] ?? '',
            'apply_complete_at' => null,
            'complete_at' => null,
            'source' => 1,
            'top' => 0,
            'top_at' => null,
            'parent_user_phone' => $parent->phone ?? '',
            'parent_user_qq' => $parent->qq ?? '',
            'take_user_qq' => '',
            'take_user_phone' => '',
            'take_parent_phone' => '',
            'take_parent_qq' => '',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ];

        $order =  static::create($data);

        // 加到redis发单队列
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
        $result = $redis->lpush('order:send', json_encode($sendOrder));

        return $order;
    }
}
