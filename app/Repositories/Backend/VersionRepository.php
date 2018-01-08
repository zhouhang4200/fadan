<?php
namespace App\Repositories\Backend;

use App\Exceptions\CustomException;
use App\Models\Version;
use DB;

// 版本号
class VersionRepository
{
    public static function dataList($name)
    {
        $dataList = Version::orderBy('name')
            ->orderBy('id', 'desc')
            ->when(!empty($name), function ($query) use ($name) {
                return $query->where('name', $name);
            })
            ->paginate(30);

        return $dataList;
    }

    // 新建
    public static function create($name, $number, $remark, $forcedUpdate)
    {
        DB::beginTransaction();

        $Version = new Version;
        $Version->name             = $name;
        $Version->number           = $number;
        $Version->current_number   = $number;
        $Version->forced_update    = $forcedUpdate ? 1 : 0;
        $Version->forced_update_id = 0;
        $Version->remark           = $remark;
        if (!$Version->save()) {
            DB::rollback();
            throw new CustomException('创建失败');
        }

        // 更新所有当前最新版本
        Version::where('name', $name)->update(['current_number' => $number]);

        // 如果创建的版本不是强制更
        if (empty($forcedUpdate)) {
            $lastVersionForcedUpdateId = Version::where('name', $name)
                ->where('id', '<>', $Version->id)
                ->orderBy('id', 'desc')
                ->value('forced_update_id');

            // 如果存在前一个版本
            if ($lastVersionForcedUpdateId) {
                $Version->forced_update_id = $lastVersionForcedUpdateId;
            } else {
                $Version->forced_update_id = $Version->id;
            }

            if (!$Version->save()) {
                DB::rollback();
                throw new CustomException('更新强制更新版本失败');
            }
        } else {
            // 更新所有版本的强制更新版本
            Version::where('name', $name)->update(['forced_update_id' => $Version->id]);
        }

        DB::commit();
        return true;
    }
}
