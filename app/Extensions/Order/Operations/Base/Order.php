<?php
namespace App\Extensions\Order\Operations\Base;

use Exception;
use App\Exceptions\OrderException;
use App\Exceptions\CustomException;
use DB;

// 订单
class Order
{
    public function handle(Operation $operation)
    {
        DB::beginTransaction();

        try {
            $operation->getObject();
            $operation->createLogObject();
            $operation->setAttributes();
            $operation->save();
            $operation->setDescription();
            $operation->saveLog();
        }
        catch (OrderException $e) {
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
