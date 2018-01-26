<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 保证金
 * Class CautionMoney
 * @package App\Models
 */
class CautionMoney extends Model
{
    public $fillable = [
        'no',
        'user_id',
        'type',
        'amount',
        'status',
        'remark',
    ];

    /**
     * @param $query
     * @param $filter
     */
    public static function scopeFilter($query, $filter)
    {
        if (isset($filter['userId']) && $filter['userId']) {
            $query->where('user_id', $filter['userId']);
        }
        return $query;
    }
}
