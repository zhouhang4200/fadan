<?php

namespace App\Models;

use Carbon\Carbon;
use App\Exceptions\OrderNoticeException;
use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;
use App\Extensions\Dailian\Controllers\PublicController;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Model
{
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
     * 手动改状态
     * @param  [type] $status [description]
     * @param  [type] $user   [description]
     * @return [type]         [description]
     */
    public function handChangeStatus($status, $user, $datas = null)
    {
        $beforeStatus = config('order.status_leveling')[$this->status];
        $handledStatus = config('order.status_leveling')[$status];

        $beforOrder = static::where('no', $this->no)->first();

        $this->status = $status;
        $this->save();

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

        OrderHistory::create($data);

        if (in_array($status, [15, 16, 19, 20, 21, 23, 24])) {
            switch ($status) {
                case 15:
                    if ($datas) {
                        $arr['user_id'] = 1;
                        $arr['order_no'] = $this->no;
                        $arr['amount'] = $datas['amount'] ?? 0;
                        $arr['deposit'] = $datas['deposit'] ?? 0;
                        $arr['consult'] = $datas['who'] ?? 0;
                        $arr['revoke_message'] = $datas['revoke_message'];
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请撤销表单！');
                    }
                break;
                case 16:
                    if ($datas) {
                        $arr['user_id'] = 1;
                        $arr['order_no'] = $this->no;
                        $arr['complain'] = $datas['who'] ?? 0;
                        $arr['complain_message'] = $datas['complain_message'];
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请仲裁表单！');
                    }
                break;
                case 19:
                    if ($datas) {
                        $arr['user_id'] = 1;
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
                case 20:
                    PublicController::completeFlows($this->no);
                break;
                case 21:
                    if ($datas) {
                        $arr['user_id'] = 1;
                        $arr['order_no'] = $this->no;
                        $arr['complain'] = $datas['who'] ?? 0;
                        $arr['complain_message'] = $datas['complain_message'];
                        LevelingConsult::where('order_no', $this->no)->updateOrCreate(['order_no' => $this->no], $arr);
                    } else {
                        throw new OrderNoticeException('没有填写申请仲裁表单！');
                    }
                    PublicController::arbitrationFlows($this->no);
                break;
                case 23:
                    PublicController::forceRevokeFlows($this->no);
                break;
                case 24:
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
}
