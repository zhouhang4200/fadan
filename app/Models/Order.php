<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use App\Exceptions\OrderNoticeException;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\DailianException;
use App\Extensions\Revisionable\RevisionableTrait;
use App\Extensions\Dailian\Controllers\PublicController;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Model
{
    protected $fillable = [
        'no', 'foreign_order_no', 'source', 'status', 'goods_id', 'goods_name', 'service_id',
        'service_name', 'game_id', 'game_name', 'original_price', 'price', 'quantity', 
        'original_amount', 'amount', 'real_amount', 'remark', 'creator_user_id', 'creator_primary_user_id',
        'gainer_user_id', 'gainer_primary_user_id', 'created_at', 'updated_at'
    ];
    
    /**
     * 订单详情
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'no');
    }

    /**
     * 订单操作历史记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_no', 'no');
    }

    /**
     * 订单资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    /**
     * 订单平台资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {

        if (isset($filters['no']) && $filters['no']) {

            $query->where('no', $filters['no']);
        } elseif (isset($filters['foreignOrderNo']) && $filters['foreignOrderNo']) {

            $query->where('foreign_order_no', $filters['foreignOrderNo']);
        } elseif (isset($filters['thirdOrderNo']) && $filters['thirdOrderNo']) {
            $no = OrderDetail::where('field_value', $filters['thirdOrderNo'])
                ->whereIn('field_name', config('leveling.third_orders'))
                ->value('order_no');
            $query->where('no', $no);
        } else {
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['creatorPrimaryUserId'])) {
                $query->where('creator_primary_user_id', $filters['creatorPrimaryUserId']);
            }

            if (isset($filters['gainerPrimaryUserId'])) {
                $query->where('gainer_primary_user_id', $filters['gainerPrimaryUserId']);
            }

            if (isset($filters['serviceId']) && $filters['serviceId']) {
                $query->where('service_id', $filters['serviceId']);
            }

            if (isset($filters['gameId']) && $filters['gameId']) {
                $query->where('game_id', $filters['gameId']);
            }

            if (isset($filters['source']) && $filters['source']) {
                $query->where('status', $filters['source']);
            }

            if (isset($filters['startDate']) &&  $filters['startDate']) {
                $query->where('created_at', '>=', $filters['startDate']);
            }

            if (isset($filters['endDate']) && $filters['endDate']) {
                $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
            }

            if (isset($filters['endDate']) && $filters['startDate']) {
                $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
            }
        }
        return $query;
    }

    /**
     * 订单外部订单关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foreignOrder()
    {
        return $this->belongsTo(ForeignOrder::class, 'foreign_order_no', 'foreign_order_no');
    }

    /**
     * 订单外部订单关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taobaoTrade()
    {
        return $this->belongsTo(TaobaoTrade::class, 'foreign_order_no', 'tid');
    }

    /**
     * 发单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }

    /**
     * 接单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gainerUser()
    {
        return $this->belongsTo(User::class, 'gainer_user_id', 'id');
    }

    /**
     * 主发单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creatorPrimaryUser()
    {
        return $this->belongsTo(User::class, 'creator_primary_user_id', 'id');
    }

    /**
     * 主接单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gainerPrimaryUser()
    {
        return $this->belongsTo(User::class, 'gainer_primary_user_id', 'id');
    }

    /**
     * 订单发罚单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function punishOrReward()
    {
        return $this->hasMany(PunishOrReward::class, 'order_id', 'no');
    }

    public function levelingConsult()
    {
        return $this->hasOne(LevelingConsult::class, 'order_no', 'no');
    }

    /**
     * 订单报警手动改状态,状态修改成功之后，在报警页面不显示这条记录
     * @param  [type] $status [description]
     * @param  [type] $user   [description]
     * @return [type]         [description]
     */
    public function handChangeStatus($status, $user, $datas = null)
    {
        // 获取订单之前和现在的状态，供操作日志记录用
        $beforeStatus = config('order.status_leveling')[$this->status];
        $handledStatus = config('order.status_leveling')[$status];
        $beforOrder = static::where('no', $this->no)->first();
        // 改订单状态
        $this->status = $status;
        $this->save();
        // 写订单操作日志
        $data['order_no'] = $this->no;
        $data['creator_primary_user_id'] = $this->creator_primary_user_id;
        $data['user_id'] = $this->creator_user_id;
        $data['admin_user_id'] = $user->id;
        $data['type'] = 0;
        $data['name'] = '手动修改订单状态';
        $data['description'] = "管理员[$user->name]将订单从[$beforeStatus]手动修改为[$handledStatus]状态!";
        $data['before'] = serialize($beforOrder->toArray());
        $data['after'] = serialize($this->toArray());
        $data['created_at'] = Carbon::now()->toDateTimeString();
        // 后台管理员手动修改订单状态，单独写的操作记录
        OrderHistory::create($data);
        // 撤销，仲裁，需要将填的信息，写到协商仲裁表里面
        if (in_array($status, [15, 16, 19, 20, 21, 23, 24])) {
            switch ($status) {
                case 15: // 撤销中
                    if ($datas) {
                        $arr['user_id'] = 0;
                        $arr['order_no'] = $this->no;
                        $arr['amount'] = $datas['amount'] ?? 0;
                        $arr['deposit'] = $datas['deposit'] ?? 0;
                        $arr['consult'] = $datas['who'] ?? 0;
                        $arr['revoke_message'] = $datas['revoke_message'];
                        // 看是发单发起撤销还是接单发起撤销
                        if ($arr['consult'] == 2) {
                            $arr['user_id'] = $this->gainer_primary_user_id;
                        } elseif ($arr['consult'] == 1) {
                            $arr['user_id'] = $this->creator_primary_user_id;
                        }
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请撤销表单！');
                    }
                    break;
                case 16: // 仲裁中
                    if ($datas) {
                        $arr['user_id'] = 0;
                        $arr['order_no'] = $this->no;
                        $arr['complain'] = $datas['who'] ?? 0;
                        $arr['complain_message'] = $datas['complain_message'];
                        // 看是发单发起撤销还是接单发起撤销
                        if ($arr['complain'] == 2) {
                            $arr['user_id'] = $this->gainer_primary_user_id;
                        } elseif ($arr['complain'] == 1) {
                            $arr['user_id'] = $this->creator_primary_user_id;
                        }
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请仲裁表单！');
                    }
                break;
                case 19: // 已撤销
                    if ($datas) {
                        $arr['user_id'] = 0;
                        $arr['order_no'] = $this->no;
                        $arr['amount'] = $datas['amount'] ?? 0;
                        $arr['deposit'] = $datas['deposit'] ?? 0;
                        $arr['consult'] = $datas['who'] ?? 0;
                        $arr['revoke_message'] = $datas['revoke_message'];
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请撤销表单！');
                    }
                    PublicController::revokeFlows($this->no);
                    break;
                case 20: // 已结算
                    PublicController::completeFlows($this->no);
                    break;
                case 21: // 已仲裁
                    if ($datas) {
                        $arr['user_id'] = 0;
                        $arr['order_no'] = $this->no;
                        $arr['complain'] = $datas['who'] ?? 0;
                        $arr['complain_message'] = $datas['complain_message'];
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请仲裁表单！');
                    }
                    PublicController::arbitrationFlows($this->no);
                    break;
                case 23: // 强制撤销
                    PublicController::forceRevokeFlows($this->no);
                    break;
                case 24: // 已删除
                    PublicController::deleteFlows($this->no);
                    break; 
            }
        }
        OrderNotice::where('order_no', $this->no)->update(['status' => $status, 'complete' => 1]);

        return true;
    }

    public function orderNotices()
    {
        return $this->hasMany(OrderNotice::class, 'order_no', 'no');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'no');
    }

    // 商品详情
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    // 充值情况
    public function orderCharge()
    {
        return $this->hasOne(OrderCharge::class, 'order_no', 'no');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function getUserAmountFlows()
    {
        return $this->hasMany(UserAmountFlow::class, 'trade_no', 'no');
    }

    public function orderApiNotices()
    {
        return $this->hasMany(OrderApiNotice::class, 'order_no', 'no');
    }

    /**
     * 获取进行中的金额
     * @return mixed
     */
    public static function ingOrderAmount()
    {
        if (auth()->user()->leveling_type == 2) {
            return self::where('creator_primary_user_id', auth()->user()->getPrimaryUserId())->where('service_id', 4)
                ->whereIn('status', [13, 14, 15, 16, 17, 18])->sum('amount');
        } else {
            return self::where('gainer_primary_user_id', auth()->user()->getPrimaryUserId())->where('service_id', 4)
                ->whereIn('status', [13, 14, 15, 16, 17, 18])->sum('amount');
        }

    }

    /**
     * 获取进行中的订单保证金
     * @return mixed
     */
    public static function ingOrderDeposit()
    {
        if (auth()->user()->leveling_type == 2) {

            return OrderDetail::where('order_no', function ($query) {
                $query->select('no')
                    ->from(with(new Order())->getTable())
                    ->where('creator_primary_user_id', auth()->user()->getPrimaryUserId())
                    ->whereIn('status', [13, 14, 15, 16, 17, 18]);
            })->whereIn('field_name', ['security_deposit', 'efficiency_deposit'])->sum('field_value');

        } else {
            return OrderDetail::where('order_no', function ($query) {
                $query->select('no')
                    ->from(with(new Order())->getTable())
                    ->where('gainer_primary_user_id', auth()->user()->getPrimaryUserId())
                    ->whereIn('status', [13, 14, 15, 16, 17, 18]);
            })->whereIn('field_name', ['security_deposit', 'efficiency_deposit'])->sum('field_value');
        }
    }


    public static function orderAndDetailAndConsult($orderNo)
    {
        $collectionArr =  DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS password,
                MAX(CASE WHEN a.field_name='source_order_no' THEN a.field_value ELSE '' END) AS source_order_no,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_type' THEN a.field_value ELSE '' END) AS game_leveling_type,
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
                MAX(CASE WHEN a.field_name='mayi_order_no' THEN a.field_value ELSE '' END) AS mayi_order_no,
                MAX(CASE WHEN a.field_name='dd373_order_no' THEN a.field_value ELSE '' END) AS dd373_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='wanzi_order_no' THEN a.field_value ELSE '' END) AS wanzi_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='order_password' THEN a.field_value ELSE '' END) AS order_password,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.status as order_status,
                b.created_at as order_created_at,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id,
                c.user_id,
                c.amount AS pay_amount,
                c.deposit,
                c.api_amount,
                c.api_deposit,
                c.api_service,
                c.status,
                c.consult,
                c.complain,
                c.complete,
                c.remark,
                c.revoke_message,
                c.complain_message
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            LEFT JOIN leveling_consults c
            ON a.order_no = c.order_no
            WHERE a.order_no='$orderNo'");
        
        $collection = is_array($collectionArr) ? $collectionArr[0] : '';

        if (empty($collection) || ! $collection->no) {
            throw new DailianException('订单号错误');
        }

        return (array) $collection;
    }
}
