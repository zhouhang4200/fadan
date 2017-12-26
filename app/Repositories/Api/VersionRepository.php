<?php
namespace App\Repositories\Api;

use App\Exceptions\CustomException;
use App\Models\Version;

// 版本号
class VersionRepository
{
    public static function ios($number)
    {
        // 查询是否存在版本号
        $Version = Version::where('name', 'ios')->where('number', $number)->first();
        if (empty($Version)) {
            throw new CustomException('不存在该版本记录');
        }

        // 如果是最新版本
        if ($Version->number == $Version->current_number) {
            $status = 1;
            $remark = '不需更新';
        } else {
            // 如果此版本高于强制更新最低版本
            if ($Version->id >= $Version->forced_update_id) {
                $status = 2;
                $remark = '建议更新';
            } else {
                $status = 3;
                $remark = '强制更新';
            }
        }

        return ['current_version' => $Version->current_number, 'status' => $status, 'remark' => $remark];
    }
}
