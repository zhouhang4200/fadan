<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Extensions\Revisionable\RevisionableTrait;

class PunishOrReward extends Model
{
    use RevisionableTrait, SoftDeletes;

	public $timestamps = true;

	protected $fillable = ['user_id', 'order_id', 'sub_money', 'deadline', 'status', 'remark', 'order_no', 'type', 'voucher', 'add_money', 'ratio', 'after_weight_value', 'before_weight_value', 'confirm', 'start_time', 'end_time'];

	protected $keepRevisionOf = array(
        'user_id', 'order_id', 'sub_money', 'deadline', 'status', 'remark', 'order_no', 'type', 'voucher', 'add_money', 'ratio', 'after_weight_value', 'before_weight_value', 'confirm', 'start_time', 'end_time'
    );

    protected $dates = ['deleted_at'];

    protected $revisionCreationsEnabled = true;

    public function user()
    {
    	return $this->belongsTo(User::Class);
    }

    public function order()
    {
    	return $this->belongsTo(Order::Class, 'order_id', 'no');
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

        if (is_numeric($filters['no'])) {

            $query->where('order_no', $filters['no']);
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
    		'order_id' => 'required',
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
