<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class GoodsTemplate extends Model
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
        'status',
        'service_id',
        'game_id',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    /**
     * 查询相同的服务与游戏是否存在模版
     * @param $serviceId
     * @param $gameId
     * @param int $id
     * @return mixed
     */
    public static function exist($serviceId, $gameId, $id = 0)
    {
        if ($id) {
            return self::where('id', '!=', $id)->where(['service_id' => $serviceId, 'game_id' => $gameId])->first();
        }
        return self::where(['service_id' => $serviceId, 'game_id' => $gameId])->first();
    }

    /**
     * 通过服务ID 与 游戏ID 获取模版ID
     * @param $serviceId
     * @param $gameId
     * @return mixed
     */
    public static function getTemplateId($serviceId, $gameId)
    {
       return  self::select('id')->where(['service_id' => $serviceId, 'game_id' => $gameId])->value('id');
    }

    public function widget()
    {
        return $this->hasMany(GoodsTemplateWidget::class);
    }
}
