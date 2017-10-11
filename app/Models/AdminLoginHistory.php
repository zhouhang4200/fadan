<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class AdminLoginHistory extends Model
{
	protected $guarded = [];

	public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
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
        $data['admin_user_id']   = $user->id;
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
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['adminUserId']) {

            $query->where('admin_user_id', $filters['adminUserId']);
        }

        if ($filters['name']) {

            $query->where('name', 'like', "%{$filters['name']}%");
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
}
