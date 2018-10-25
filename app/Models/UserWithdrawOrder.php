<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Unfreeze;
use DB;
use Asset;
use Auth;
use App\Extensions\Revisionable\RevisionableTrait;

class UserWithdrawOrder extends Model
{
    use RevisionableTrait;

    /**
     * 开启监听
     * @var bool
     */
    protected $revisionCreationsEnabled = true;

    /**
     * 自动清除记录
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * 保存多少条记录
     * @var int
     */
    protected $historyLimit = 50000;

    /**
     * 不监听的字段
     * @var array
     */
    protected $dontKeepRevisionOf = ['id'];


    // 提现完成
    public function complete($remark, $type = 1)
    {
        DB::beginTransaction();

        $this->lockForUpdate()->find($this->id);

        // 提现
        try {
            Asset::handle(new Withdraw($this->fee, $type, $this->no, $remark, $this->creator_primary_user_id, Auth::user()->id));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->status = 2;
        $this->admin_remark = $remark;

        if (!$this->save()) {
            DB::rollback();
            throw new Exception('操作失败');
        }

        // 写多态关联
        if (!$this->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
    }

    // 拒绝提现
    public function refuse()
    {
        DB::beginTransaction();

        $this->lockForUpdate()->find($this->id);

        // 解冻
        try {
            Asset::handle(new Unfreeze($this->fee, Unfreeze::TRADE_SUBTYPE_WITHDRAW, $this->no, '拒绝提现解冻', $this->creator_primary_user_id, Auth::user()->id));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->status = 3;

        if (!$this->save()) {
            DB::rollback();
            throw new Exception('操作失败');
        }

        // 写多态关联
        if (!$this->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
    }

    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    /**
     * 关联user 模型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'creator_primary_user_id');
    }

    /**
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public static function scopeFilter($query, $filter = [])
    {
        if ($filter['startDate']) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if ($filter['endDate']) {
            $query->where('created_at', '<=', $filter['endDate']." 23:59:59");
        }

        if ($filter['status']) {
            $query->where('status', $filter['status']);
        }
        return $query;
    }
}
