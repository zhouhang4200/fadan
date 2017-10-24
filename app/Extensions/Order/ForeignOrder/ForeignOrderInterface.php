<?php

namespace App\Extensions\Order\ForeignOrder;

interface ForeignOrderInterface
{
    public function parseAndCreateOrder($data);
}
