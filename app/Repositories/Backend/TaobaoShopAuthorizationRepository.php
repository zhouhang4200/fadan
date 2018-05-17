<?php
namespace App\Repositories\Backend;

use App\Models\TaobaoShopAuthorization;
use App\Exceptions\CustomException;
use DB;
use App\Models\User;

class TaobaoShopAuthorizationRepository
{
    public static function getList($userId, $wangWang)
    {
        $dataList = TaobaoShopAuthorization::orderBy('id', 'desc')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($wangWang, function ($query) use ($wangWang) {
                return $query->whereIn('wang_wang', $wangWang);
            })
            ->paginate(20);
        return $dataList;
    }

    public static function getShops()
    {
        $names = TaobaoShopAuthorization::distinct()->pluck('wang_wang');

        foreach ($names as $name) {
            $dataList[] = [
                'id'   => $name,
                'name' => $name,
                'pid'  => 0,
            ];
        }

        return $dataList;
    }

    public static function store($userId, $wangWang)
    {
        $user = User::find($userId);
        if (empty($user)) {
            throw new CustomException('商户ID不存在');
        }

        // 查询需要添加的店铺
        DB::beginTransaction();
        foreach ($wangWang as $v) {
            $model = TaobaoShopAuthorization::where('user_id', $userId)->where('wang_wang', $v)->first();
            if ($model) {
                continue;
            }

            $model = new TaobaoShopAuthorization;
            $model->user_id = $userId;
            $model->wang_wang = $v;
            if (!$model->save()) {
                DB::rollback();
                throw new CustomException('操作失败');
            }
        }

        DB::commit();
        return true;
    }

    public static function destroy($id)
    {
        $data = TaobaoShopAuthorization::find($id);
        if (empty($data)) {
            throw new CustomException('数据不存在');
        }

        if (!$data->delete()) {
            throw new CustomException('删除失败');
        }

        return true;
    }
}
