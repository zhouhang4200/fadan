<?php
namespace App\Repositories\Api;

use App\Exceptions\CustomException;
use App\Models\Version;

// 版本号
class VersionRepository
{
    public static function market($name, $number)
    {
        // 获取最新版本
        $CurrentVersion = Version::where('name', $name)->orderBy('id', 'desc')->first();

        // 查询传入的版本号
        $Version = Version::where('name', $name)->where('number', $number)->first();
        if (empty($Version)) {
            $status = 3;
        } else {
            // 如果是最新版本
            if ($Version->number == $Version->current_number) {
                $status = 1;
            } else {
                // 如果此版本高于强制更新最低版本
                if ($Version->id >= $Version->forced_update_id) {
                    $status = 2;
                } else {
                    $status = 3;
                }
            }
        }

        return ['current_version' => $CurrentVersion->current_number, 'status' => $status, 'remark' => $CurrentVersion->remark];
    }
}
