<?php

namespace Layer\Base\Exceptions;

use Exception;

/**
 * 异常基类
 * Class BaseException
 * @package Omics\Zendo\Exceptions
 */
class BaseException extends Exception
{
    // 常见错误错误信息
    protected array $baseErrors = [];

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
