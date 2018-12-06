<?php

namespace App\Exceptions;

use App\Http\Controllers\Api\ApiResponse;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

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
     * @param  \Exception $exception
     * @return void
     * @throws Exception
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
        if ($exception instanceof NotFoundHttpException && stristr(request()->fullUrl(), 'api')) {
            return $this->notFond('没有找到相关资源');
        }
        // 参数验证错误的异常，我们需要返回 400 的 http code 和错误信息
        if ($exception instanceof ValidationException  && stristr(request()->fullUrl(), 'api')) {
            return $this->failed($exception->errors(), 400);
        }
        // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
        if ($exception instanceof UnauthorizedHttpException  && stristr(request()->fullUrl(), 'api')) {
            return $this->failed('身份认证失败', 401);
        }
        // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
        if ($exception instanceof TokenInvalidException  && stristr(request()->fullUrl(), 'api')) {
            return $this->failed('身份认证失败', 401);
        }
        // 前端分离分后无法用route 方法重定向，需用 redirect 重定向到前端的logo路由，
        if ($exception instanceof AuthenticationException) {
            return redirect('/login');
        }

        if (! $exception instanceof HttpExceptionInterface && stristr(request()->fullUrl(), 'api')) {
            return $this->internalError('服务器错误' . $exception->getFile(). $exception->getMessage());
        }

        return parent::render($request, $exception);
    }
}
