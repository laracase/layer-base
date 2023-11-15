<?php

namespace Layer\Base\Support;

use Exception;

/**
 * 异常基类
 * Class BaseException
 * @package Omics\Zendo\Exceptions
 */
class BaseException extends Exception
{
    // 常见错误错误信息
    private array $baseErrors = [
        'scope' => [
            'status' => 401,
            'message' => '网关错误',
        ],
        'token' => [
            'status' => 401,
            'message' => '没有登录',
        ],
        'consumer' => [
            'status' => 401,
            'message' => '网关错误',
        ],
        'error.password' => [
            'status' => 401,
            'message' => '密码错误',
        ],
        'oauth' => [
            'status' => 401,
            'message' => '第三方授权认证失败',
        ],
        'block' => [
            'status' => 503,
            'message' => '系统维护中～',
        ],
        'upload.error' => [
            'message' => '上传失败',
        ],
        'slug.exists' => [
            'message' => 'slug重复',
        ],
        'not.name' => [
            'message' => '未定义事件名称',
        ],
        'not.listener' => [
            'message' => '未定义事件处理监听器',
        ],
        'not.job' => [
            'message' => '未定义事件相关处理任务',
        ],
    ];

    // 错误信息
    protected array $errors = [];

    protected int $statusCode = 500;

    /**
     * BaseException constructor.
     * @param string $type
     * @param string $message
     */
    public function __construct(string $type = '', string $message = '')
    {
        $errors = array_merge($this->baseErrors, $this->errors);
        if (!empty($errors[$type])) {
            $message = $errors[$type]['message'];
            $code = $errors[$type]['code'] ?? -1;
            $this->statusCode = $errors[$type]['status'] ?? $this->statusCode;
        } else {
            $message = $message ?: '未知系统错误';
            $code = -1;
        }
        parent::__construct($message, $code);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
