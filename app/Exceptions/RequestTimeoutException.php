<?php
namespace App\Exceptions;

use Exception;

/**
 * 请求超时异常报警异常
 * Class RequestTimeoutException
 * @package App\Exceptions
 */
class RequestTimeoutException extends Exception
{
    /**
     * RequestTimeoutException constructor.
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        // 写入报警逻辑

    }

}
