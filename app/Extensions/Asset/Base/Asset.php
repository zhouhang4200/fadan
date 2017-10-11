<?php
namespace App\Extensions\Asset\Base;

use App\Exceptions\AssetException;
use App\Exceptions\CustomException;
use DB;

// 资产
class Asset
{
    public function handle(Trade $trade)
    {
        DB::beginTransaction();

        try {
            $trade->beforeUser();
            $trade->updateUserAsset();
            $trade->writeUserAmountFlow();
            $trade->beforePlatform();
            $trade->updatePlatformAsset();
            $trade->writePlatformAmountFlow();
            $trade->after();
        }
        catch (AssetException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage());
        }

        DB::commit();
        return true;
    }
}
