<?php

namespace App\Extensions\Order\ForeignOrder;

use Exception;

abstract class ForeignOrder implements ForeignOrderInterface
{
    public function parseAndCreateOrder($data)
    {
    	try {

            return $this->outputOrder($data);

        } catch (Exception $e) {

            Log::warning('参数格式传入错误!', ['data' => $data]);          
        }
    }

    abstract public function outputOrder($data);
}
