<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            if ($request->expectsJson()) {
                $errors = $exception->validator->errors()->getMessages();
                return response()->fail(-2, "validation fail", $errors);
            }
        }
        if ($exception instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->fail(-1, "Route Not Found", [], [], 404);
            }
        }
        if ($exception instanceof BusinessErrorException) {
            if ($request->expectsJson()) {
                return response()->fail(-1, $exception->getMessage());
            }
            // 采用与500错误一样的模板，当然也可以自定义一个不一样的模板；
            // 但是视图类的返回值一定要200，业务错误属于正常错误，只是告知用户，因此应该与一般的500错误区分开
            return response()->view('errors.500', ['exception' => $exception]);
        }
        if ($request->is('api/*')) {
            if ($exception instanceof  HttpException) {
                return response()->fail(-1, 'STATUS '. $exception->getStatusCode() . ':' . $exception->getMessage(), [], [], $exception->getStatusCode());
            }
            return response()->fail(-1, $exception->getMessage(), [], [], 500);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
