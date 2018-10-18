<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 参数验证错误的异常，我们需要返回 400 的 http code 和错误信息
        if ($exception instanceof NotFoundHttpException && stristr(request()->fullUrl(), 'open-api')) {
            return response(['error' => "没有找到相关资源"], 404);
        }
        // 参数验证错误的异常，我们需要返回 400 的 http code 和错误信息
        if ($exception instanceof ValidationException  && stristr(request()->fullUrl(), 'open-api')) {
            return response(['error' => array_first(array_collapse($exception->errors()))], 400);
        }
        // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
        if ($exception instanceof UnauthorizedHttpException  && stristr(request()->fullUrl(), 'open-api')) {
            return response(['error' => '登录信息失效'], 401);
        }

        if (! $exception instanceof HttpExceptionInterface && stristr(request()->fullUrl(), 'open-api')) {
            return response(['error' => "服务器错误"], 500);
        }

        return parent::render($request, $exception);
    }
}
