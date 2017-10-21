<?php
namespace App\Extensions\Asset\Base;

use Exception;
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
            $trade->createUserAmountFlow();
            $trade->beforePlatform();
            $trade->updatePlatformAsset();
            $trade->createPlatformAmountFlow();
        }
        catch (AssetException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage());
        }
        catch(Exception $e) {
            DB::rollBack();
            abort(404);
        }

        DB::commit();
        return true;
    }
}
