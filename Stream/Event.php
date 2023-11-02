<?php

namespace Layer\Base\Stream;

class Event
{
    /**
     * Create a event
     *
     * @param string $name 事件名称
     * @param array $sender 发送者
     * @param array $receiver 接收者
     * @param array $payload 事件内容
     * @param string $schema 时间模式
     */
    public function __construct(
        public string $name,
        public array  $sender = [],
        public array  $receiver = [],
        public array  $payload = [],
        public string $schema = "1.0",
    )
    {
    }
}
