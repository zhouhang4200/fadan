<?php
namespace App\Extensions\Weight;

/**
 * Class Facade
 * @package App\Extensions\Weight
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return Weight::class;
    }
}
