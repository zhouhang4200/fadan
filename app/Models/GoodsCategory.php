<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class GoodsCategory extends Model
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
        'parent_id',
        'name',
        'created_admin_user_id',
        'updated_admin_user_id',
        'template_id',
    ];

    /**
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeName($query, $name)
    {
        return $query;
    }

    /**
     * @param $query
     * @param $parentId
     * @return mixed
     */
    public function scopeParentId($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * 关联父级的信息
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * 创建数据 管理员的信息
     */
    public function createdAdmin()
    {
        return $this->hasOne(AdminUser::class, 'id', 'created_admin_user_id');
    }

    /**
     * 修改信息的 管理员的信息
     */
    public function updatedAdmin()
    {
        return $this->hasOne(AdminUser::class, 'id', 'updated_admin_user_id');
    }
}
