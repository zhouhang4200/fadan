<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelOrder extends Model
{
    public $fillable = [
        'trade_no',
        'user_id',
        'game_leveling_channel_user_id',
        'amount',
        'supply_amount',
        'discount_amount',
        'payment_amount',
        'payment_at',
        'refund_amount',
        'payment_type',
        'status',
        'game_id',
        'game_name',
        'game_region_id',
        'game_region_name',
        'game_server_id',
        'game_server_name',
        'game_leveling_type_id',
        'game_leveling_type_name',
        'game_role',
        'game_account',
        'game_password',
        'player_phone',
        'player_qq',
        'user_qq',
        'title',
        'day',
        'hour',
        'demand',
        'security_deposit',
        'efficiency_deposit',
        'explain',
        'requirement',
        'remark',
    ];

    protected $appends = ['visible'];

    /**
     * 渠道C端用户表
     */
    public function gameLevelingChannelUser()
    {
        $this->belongsTo(GameLevelingChannelUser::class);
    }

    public function gameLevelingOrders()
    {
        return $this->hasMany(GameLevelingOrder::class, 'channel_order_trade_no', 'trade_no');
    }

    /**
     * 渠道列表筛选
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public function scopeFilter($query, $filter = [])
    {
        if (isset($filter['trade_no']) && !empty($filter['trade_no'])) {
            $query->where('trade_no', $filter['trade_no']);
        }

        if (isset($filter['game_id']) && !empty($filter['game_id'])) {
            $query->where('game_id', $filter['game_id']);
        }

        if (isset($filter['status']) && !empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        if (isset($filter['startDate']) && !empty($filter['startDate'])) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if (isset($filter['endDate']) && !empty($filter['endDate'])) {
            $query->where('created_at', '<=', $filter['endDate']);
        }
        return $query;
    }

    public function gameLevelingChannelRefund()
    {
        return $this->hasMany(GameLevelingChannelRefund::class, 'game_leveling_channel_order_trade_no', 'trade_no');
    }

    /**
     * 计算 代练价格、时间、保证金
     * @param $userId integer 用户ID
     * @param $gameId integer 游戏ID
     * @param $gameLevelingTypeId integer 代练游戏类型
     * @param $currentLevelId integer 当前段位ID
     * @param $targetLevelId integer 目标段位ID
     * @return object
     */
    public static function amountTimeDepositCompute($userId, $gameId, $gameLevelingTypeId, $currentLevelId, $targetLevelId)
    {
        # 获取渠道游戏
        $gameLevelingChannelGame = GameLevelingChannelGame::where('game_id', $gameId)
            ->where('user_id', $userId)
            ->where('game_leveling_type_id', $gameLevelingTypeId)
            ->first();

        # 获取当前段位
        $currentLevel = $gameLevelingChannelGame->gameLevelingChannelPrices()
            ->where('id', $currentLevelId)
            ->first();

        # 获取目标段位
        $targetLevel = $gameLevelingChannelGame->gameLevelingChannelPrices()
            ->where('id', $targetLevelId)
            ->first();

        $prices = $gameLevelingChannelGame->gameLevelingChannelPrices()
            ->where('sort', '>=', $currentLevel->sort)
            ->where('sort', '<', $targetLevel->sort)
            ->get();

        # 代练价格
        $price = $prices->sum('price');

        # 效率保证金
        $securityDeposit = $prices->sum('security_deposit');

        # 安全保证金
        $efficiencyDeposit = $prices->sum('efficiency_deposit');

        # 安全保证金
        $hour = $prices->sum('hour');

        # 总共代练级别
        $totalLevel = $prices->count();

        # 根据总共的代练等级，获取折扣，如没有设置则不打折
        $discount = $gameLevelingChannelGame->gameLevelingChannelDiscounts()
                ->where('level', '<=', $totalLevel)
                ->min('discount') ?? 100;

        # C端用户需支付的订单金额
        $amount = bcmul($price, bcdiv($discount, 100, 2), 2);

        # 假的优惠金额只用于前台显示
        $fakeAmount = bcmul($amount, rand(1.5, 5), 2);

        # 获取发单折扣
        $supplyDiscount = $gameLevelingChannelGame->rebate ?? 100;

        # 发单价格
        $supplyAmount = bcmul($amount, bcdiv($supplyDiscount, 100, 2), 2);

        # 展示的需要代练的时间
        $showTime = trim(sec2Time($hour * 3600));

        return (object) array_merge([
            'security_deposit' => $securityDeposit,
            'efficiency_deposit' => $efficiencyDeposit,
            'amount' => $amount,
            'fake_amount' => $fakeAmount,
            'supply_amount' => $supplyAmount,
            'show_time' => $showTime,
            'current_level' => $currentLevel,
            'target_level' => $targetLevel,
            'hour' => 1,
            'day' => 0,
        ], $gameLevelingChannelGame->toArray());
    }

    /**
     * 用于前端按钮是否显确认弹窗
     * @return bool
     */
    public function getVisibleAttribute()
    {
        return false;
    }
}
