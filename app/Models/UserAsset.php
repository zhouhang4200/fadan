<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取用户余额
     * @param $userId
     * @return mixed
     */
    public static function balance($userId)
    {
        return self::where('user_id', $userId)->value('balance');
    }
}
