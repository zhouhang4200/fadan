<?php
namespace App\Extensions\Asset\Base;

use Exception;
use App\Exceptions\AssetException;
use App\Exceptions\CustomException;
use DB;

// 资产
class Asset
{
    private $_userAmountFlow;
    private $_platformAmountFlow;

    public function handle(Trade $trade)
    {
        DB::beginTransaction();

        try {
            $trade->beforeUser();
            $trade->updateUserAsset();
            $this->_userAmountFlow = $trade->createUserAmountFlow();
            $trade->beforePlatform();
            $trade->updatePlatformAsset();
            $this->_platformAmountFlow = $trade->createPlatformAmountFlow();
        }
        catch (AssetException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage());
        }

        DB::commit();
        return true;
    }

    public function getUserAmountFlow()
    {
        return $this->_userAmountFlow;
    }

    public function getPlatformAmountFlow()
    {
        return $this->_platformAmountFlow;
    }
}
