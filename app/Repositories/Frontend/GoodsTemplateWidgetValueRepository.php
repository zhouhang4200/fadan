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
     * @param $id
     * @return mixed
     */
    public static function getTags($userId)
    {
        $tags = GoodsTemplateWidgetValue::where('field_name', 'lable')
            ->where(function ($query) use ($userId) {
                $query->where('goods_template_widget_id', 0);
                $query->orWhere('user_id', $userId);
            })
            ->pluck('field_value', 'id');

        return $tags;
    }
}
