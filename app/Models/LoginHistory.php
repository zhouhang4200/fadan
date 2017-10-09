<?php

namespace App\Models;

use Auth;
use App\User;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(static::class, 'pid', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'pid', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDetailAttribute($value)
    {
        return json_decode($value);
    }

    public function setDetailAttribute($value)
    {
        return $this->attributes['detail'] = json_encode($value);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * 写入登录信息
     * @param  Request
     * @return \Illuminate\Database\Eloquent\Model|$this
     */
    protected function writeLoginHistory($ip)
    {
        // 获取登录详情
        $detailArray = loginDetail($ip);
        // 获取登录用户详情
        $user              = Auth::user();
        $data['pid']       = $user->pid;
        $data['user_id']   = $user->id;
        $data['user_type'] = $user->type;
        $data['ip']        = $detailArray['ip'];
        $data['city_id']   = $detailArray['city_id'] ?: 0;
        $data['detail']    = json_encode($detailArray) ?: '';
        // 写入登录信息表
        static::create($data);
    }

    /**
     * 查找 显示 登录详情
     * @var [type]
     */
    public static function scopeFilters($query, $filters = [])
    {
        if ($filters['userId']) {

            $query->where('user_id', $filters['userId']);
        }

        if ($filters['name']) {

            $query->where('name', $filters['name']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('create_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('create_at', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('create_at', [$filters['startDate'], $filters['endDate']]);
        }

        return $query;
    }
}
