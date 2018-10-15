<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class Game extends Model
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

    public $fillable = [
      'name',
      'sortord',
      'status',
      'created_admin_user_id',
      'updated_admin_user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function createdAdmin()
    {
        return $this->hasOne(AdminUser::class, 'id', 'created_admin_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function updatedAdmin()
    {
        return $this->hasOne(AdminUser::class, 'id', 'updated_admin_user_id');
    }

    public function goodses()
    {
        return $this->hasMany(Goods::Class);
    }

    // 搜索
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['name']) {
            $query->where('name', $filters['name']);
        }
        return $query;
    }

    public function UserReceivingCategoryControls()
    {
        return $this->hasMany(UserReceivingCategoryControl::class);
    }

    /**
     *  一对多，区
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    /**
     * 一对多，代练类型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameLevelingTypes()
    {
        return $this->hasMany(GameLevelingType::class);
    }
}
