<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

/**
 * 用户设置存储
 * Class UserSetting
 * @package App\Models
 */
class UserSetting extends Model
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

    /**
     * 不监听的字段
     * @var array
     */
    protected $dontKeepRevisionOf = ['id'];

    public $fillable = [
      'user_id',
      'option',
      'value',
    ];


}
