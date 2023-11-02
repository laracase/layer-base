<?php

namespace Layer\Base\Exceptions;

/**
 * 逻辑异常
 * Class LogicException
 * @package Omics\Zendo\Exceptions
 */
class BaseLogicException extends BaseException
{
    // 错误信息
    protected array $baseErrors = [
        'upload.error' => [
            'message' => '上传失败',
        ],
        'slug.exists' => [
            'message' => 'slug重复',
        ],
    ];
}
