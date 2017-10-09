<?php
namespace App\Extensions\Amount\Base;

use App\Exceptions\AmountException;
use App\Exceptions\CustomException;
use DB;

// 资金
class Amount
{
    public function handle(Transaction $transaction)
    {
        DB::beginTransaction();

        try {
            $transaction->before();
            $transaction->updateUserAmount();
            $transaction->writeUserFlow();
            $transaction->updatePlatformAmount();
            $transaction->writePlatformFlow();
            $transaction->after();
        }
        catch (AmountException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage());
        }

        DB::commit();
        return true;
    }
}
