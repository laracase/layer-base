<?php

namespace Layer\Base\Exceptions;

class BaseEventException extends BaseException
{
    protected array $baseErrors = [
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
}
