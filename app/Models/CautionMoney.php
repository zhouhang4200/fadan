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
}
