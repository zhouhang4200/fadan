<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class Goods extends Model
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
        'user_id',
        'display',
        'price',
        'foreign_goods_id',
        'service_id',
        'game_id',
        'sortord',
        'loss',
        'goods_template_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function goodsTemplate()
    {
        return $this->belongsTo(GoodsTemplate::class);
    }
}
