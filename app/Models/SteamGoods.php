<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

class SteamGoods extends Model
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

    /**
     * @return array
     */
    public static function createdGoods()
    {
        return [
            'name' => 'required|unique:goods',
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required' => '请填写账号！',
            'name.unique' => '账号已经存在！',
        ];
    }
}
