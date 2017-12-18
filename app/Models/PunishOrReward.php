<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Extensions\Revisionable\RevisionableTrait;

class PunishOrReward extends Model
{
    use RevisionableTrait, SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['user_id', 'order_no', 'sub_money', 'deadline', 'status', 'remark', 'no', 'type', 'voucher', 'add_money', 'ratio', 'after_weight_value', 'before_weight_value', 'confirm', 'start_time', 'end_time'];

    protected $keepRevisionOf = array(
        'user_id', 'order_no', 'sub_money', 'deadline', 'status', 'remark', 'no', 'type', 'voucher', 'add_money', 'ratio', 'after_weight_value', 'before_weight_value', 'confirm', 'start_time', 'end_time', 'deleted_at'
    );

    protected $dates = ['deleted_at'];

    protected $revisionCreationsEnabled = true;

     /**
     * Called after a model is successfully saved.
     *
     * @return void
     */
    public function postSave()
    {
        if (isset($this->historyLimit) && $this->revisionHistory()->count() >= $this->historyLimit) {
            $LimitReached = true;
        } else {
            $LimitReached = false;
        }
        if (isset($this->revisionCleanup)){
            $RevisionCleanup=$this->revisionCleanup;
        }else{
            $RevisionCleanup=false;
        }

        // check if the model already exists
        if (((!isset($this->revisionEnabled) || $this->revisionEnabled) && $this->updating) && (!$LimitReached || $RevisionCleanup)) {
            // if it does, it means we're updating

            $changes_to_record = $this->changedRevisionableFields();

            $revisions = array();

            $name = '系统';
            if ($this->getUser()) {
                if ($this->getUser()->getTable() == 'users') {
                    $name = '商户： ' . User::where('id', $this->getSystemUserId())->value('name');
                } else {
                    $name = '管理员： ' . AdminUser::where('id', $this->getSystemUserId())->value('name');
                }
            }

            foreach ($changes_to_record as $key => $change) {
                $revisions[] = array(
                    'punish_or_reward_id' => $this->getKey(),
                    'operate_style' => $key,
                    'punish_or_reward_no' => $this->no,
                    'order_no' => $this->order_no,
                    'before_value' => array_get($this->originalData, $key),
                    'after_value' => $this->updatedData[$key],
                    'admin_user_name' => $name,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                );
            }
           
            if (count($revisions) > 0) {
                if($LimitReached && $RevisionCleanup){
                    $toDelete = $this->revisionHistory()->orderBy('id','asc')->limit(count($revisions))->get();
                    foreach($toDelete as $delete){
                        $delete->delete();
                    }
                }
                $revision = new PunishOrRewardRevision;
                \DB::table($revision->getTable())->insert($revisions);
                \Event::fire('revisionable.saved', array('model' => $this, 'revisions' => $revisions));
            }
        }
    }

    /**
    * Called after record successfully created
    */
    public function postCreate()
    {

        // Check if we should store creations in our revision history
        // Set this value to true in your model if you want to
        if(empty($this->revisionCreationsEnabled))
        {
            // We should not store creations.
            return false;
        }

        if ((!isset($this->revisionEnabled) || $this->revisionEnabled))
        {
            $name = '系统';
            if ($this->getUser()) {
                if ($this->getUser()->getTable() == 'users') {
                    $name = '商户： ' . User::where('id', $this->getSystemUserId())->value('name');
                } else {
                    $name = '管理员： ' . AdminUser::where('id', $this->getSystemUserId())->value('name');
                }
            }

            $revisions[] = array(
                'punish_or_reward_id' => $this->getKey(),
                'operate_style' => self::CREATED_AT,
                'punish_or_reward_no' => $this->no,
                'order_no' => $this->order_no,
                'before_value' => null,
                'after_value' => $this->{self::CREATED_AT},
                'admin_user_name' => $name,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );
            $revision = new PunishOrRewardRevision;
            \DB::table($revision->getTable())->insert($revisions);
            \Event::fire('revisionable.created', array('model' => $this, 'revisions' => $revisions));
        }
    }

    /**
     * If softdeletes are enabled, store the deleted time
     */
    public function postDelete()
    {
        if ((!isset($this->revisionEnabled) || $this->revisionEnabled)
            && $this->isSoftDelete()
            && $this->isRevisionable($this->getDeletedAtColumn())
        ) {
            $name = '系统';
            if ($this->getUser()) {
                if ($this->getUser()->getTable() == 'users') {
                    $name = '商户： ' . User::where('id', $this->getSystemUserId())->value('name');
                } else {
                    $name = '管理员： ' . AdminUser::where('id', $this->getSystemUserId())->value('name');
                }
            }

            $revisions[] = array(
                'punish_or_reward_id' => $this->getKey(),
                'operate_style' => $this->getDeletedAtColumn(),
                'punish_or_reward_no' => $this->no,
                'order_no' => $this->order_no,
                'before_value' => null,
                'after_value' => $this->{$this->getDeletedAtColumn()},
                'admin_user_name' => $name,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );
           
            $revision = new PunishOrRewardRevision;
            \DB::table($revision->getTable())->insert($revisions);
            \Event::fire('revisionable.deleted', array('model' => $this, 'revisions' => $revisions));
        }
    }

    public function user()
    {
    	return $this->belongsTo(User::Class);
    }

    public function order()
    {
    	return $this->belongsTo(Order::Class, 'order_no', 'no');
    }

    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    public static function scopeFilter($query, $filters = [])
    {
        if (is_numeric($filters['type'])) {

            $query->where('type', $filters['type']);
        }

        if (is_numeric($filters['orderNo'])) {

            $query->where('order_no', $filters['orderNo']);
        }

        if (is_numeric($filters['status'])) {

            $query->where('status', $filters['status']);
        }

        if ($filters['userId']) {

            $query->where('user_id', $filters['userId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }

        return $query;
    }

    public static function rules()
    {
    	return [
    		'order_no' => 'required',
    		'user_id' => 'required|numeric',
    	];
    }

    public static function messages()
    {
    	return [
    		'user_id.required' => '用户id必须填写',
    	];
    }

    public static function scopeHomeFilter($query, $filters = [])
    {
        if (is_numeric($filters['type'])) {

            $query->where('type', $filters['type']);
        }

        if (is_numeric($filters['status'])) {

            $query->where('status', $filters['status']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }

        return $query->latest('created_at');
    }

    public function getVoucherAttribute($value)
    {
        return json_decode($value);
    }

    public function setVoucherAttribute($value)
    {
        $this->attributes['voucher'] = json_encode($value);
    }

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::Class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'revisionable_id', 'id');
    }
}
