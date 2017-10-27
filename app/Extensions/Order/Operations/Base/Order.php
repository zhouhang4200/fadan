<?php
namespace App\Extensions\Order\Operations\Base;

use Exception;
use App\Exceptions\OrderException;
use App\Exceptions\CustomException;
use DB;

// 订单
class Order
{
    private $_order;

    public function handle(Operation $operation)
    {
        DB::beginTransaction();

        try {
            $operation->getObject();
            $operation->createLogObject();
            $operation->setAttributes();
            $this->_order = $operation->save();
            $operation->updateAsset();
            $operation->setDescription();
            $operation->saveLog();
        }
        catch (OrderException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage());
        }

        DB::commit();
        return true;
    }

    public function get()
    {
        return $this->_order;
    }
}
