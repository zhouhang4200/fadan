<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class Punish extends Model
{
	use RevisionableTrait;

	public $timestamps = true;

	protected $fillable = ['user_id', 'order_id', 'money', 'deadline', 'remark', 'order_no', 'type'];

	protected $keepRevisionOf = array(
        'order_id', 'user_id', 'money', 'type', 'remark', 'deadline'
    );

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
    		'remark' => 'required',
    		'money' => 'required',
    		'deadline' => 'required|date',
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
}
