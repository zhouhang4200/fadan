<?php
namespace App\Repositories\Frontend;

use Auth;
use App\Models\GoodsTemplateWidgetValue;

/**
 * Class GoodsRepository
 * @package App\Repositories\Frontend
 */
class GoodsTemplateWidgetValueRepository
{
    /**
     * @param $userId
     * @return mixed
     * @internal param $id
     */
    public static function getTags($userId)
    {
        $tags = GoodsTemplateWidgetValue::where('field_name', 'label')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', 0);
            })
            ->groupBy('field_value')
            ->pluck('field_value', 'field_value');

        return $tags;
    }
}
