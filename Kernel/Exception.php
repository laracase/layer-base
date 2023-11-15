<?php

namespace Layer\Base\Kernel;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Layer\Base\Support\BaseException;
use Layer\Base\Traits\ResponseTrait;
use PDOException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Exception extends Handler
{
    use ResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * 写入文件中，然后用logtail汇总到阿里云
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse
    {
        // 认证错误统一返回401
        if ($e instanceof BaseException && $e->getStatusCode() === 401) {
            return $this->sendFailedJson($e->getStatusCode(), $e->getMessage(), $e->getCode());
        }
        // 非调试环境，错误提示友好
        if (!config('app.debug')) {
            // 自定义逻辑错误
            if ($e instanceof BaseException) {
                return $this->sendFailedJson($e->getStatusCode(), $e->getMessage(), $e->getCode());
            }

            // 路由不存在
            if ($e instanceof NotFoundHttpException) {
                if (app()->environment('production')) {
                    // 正式环境直接跳转
                    $this->redirect();
                } else {
                    return $this->sendFailedJson(404, '非法请求，路由不存在');
                }
            }

            // 数据格式验证不通过
            if ($e instanceof ValidationException) {
                $message = $e->getMessage();
                if (str_starts_with($message, 'validation.')) {
                    $message = '请求数据非法～';
                }
                return $this->sendFailedJson(422, $message);
            }

            // 数据验证不通过
            if ($e instanceof ModelNotFoundException) {
                return $this->sendFailedJson(404, '非法请求，数据不存在');
            }

            if ($e instanceof PDOException) {
                // sql异常
                return $this->sendFailedJson(522, '系统错误～');
            }
            // 其他错误，系统内部错误
            return $this->sendFailedJson(521, '未知错误～', $e->getCode());
        } else {
            return $this->prepareJsonResponse($request, $e);
        }
    }
}
