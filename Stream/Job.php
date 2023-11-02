<?php

namespace Layer\Base\Stream;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public function __construct(public array $event)
    {
    }


    //

    /**
     * Illuminate/Bus/Dispatcher.php  => dispatchToQueue
     * @param Queue $queue
     * @param $command
     * @return mixed
     */
    public function queue($queue, $command)
    {
        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn($command->queue, $command->delay, $command);
        }

        if (isset($command->queue)) {
            return $queue->pushOn($command->queue, $command);
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        // @todo 这里做处理，和其他 queue 通信
        // push 到 redis 队列中
        // Illuminate\Queue\RedisQueue::push
        // $job, $data, $queue
        // 如果 $job 是对象，则是对象序列化的形式，
        // 如果 $job 是字符串，则 json 形式
        $data = [];
        return $queue->push($command, $data);
    }
}
