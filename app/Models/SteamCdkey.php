<?php

namespace App\Models;

use App\Extensions\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SteamCdkey extends Model
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

    //黑名单为空
    protected $guarded = [];

    public function goodses()
    {
        return $this->hasOne(SteamGoods::class,'id', 'goods_id');
    }

    public function cdkeyLibrary()
    {
        return $this->hasMany(SteamCdkeyLibrary::class);
    }


}
