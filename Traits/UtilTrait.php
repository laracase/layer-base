<?php

namespace Layer\Base\Traits;

use Exception;
use MongoDB\BSON\ObjectId;

trait UtilTrait
{
    public static int $true = 1;
    public static int $false = 0;


    public function toOid($value)
    {
        if (is_array($value)) {
            array_walk($value, function (&$item) {
                $item = new ObjectId($item);
            });
            return $value;
        } else {
            return new ObjectId($value);
        }
    }

    /**
     * 开始 xhprof
     * @return void
     */
    public function startProf(): void
    {
        xhprof_enable(XHPROF_FLAGS_NO_BUILTINS + XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }

    /**
     * 结束 xhprof 并保存文件
     * @return void
     */
    public function endProf(): void
    {
        $filename = date('YmdHis') . '.json';
        file_put_contents(storage_path('logs') . '/' . $filename, json_encode(xhprof_disable()));
    }

    /**
     * @param string $action
     * @return string
     */
    public function action2field(string $action): string
    {
        $actionFields = [
            'follow' => 'is_follow',
            'like' => 'is_like',
            'mood' => 'mood',
            'member' => 'member',
        ];
        return $actionFields[$action] ?? 'is_like';
    }

    /**
     * @param string $action
     * @return string
     */
    public function action4count(string $action): string
    {
        $actionFields = [
            'follow' => 'follow_count',
            'like' => 'like_count',
            'view' => 'view_count',
            'reply' => 'reply_count',
        ];
        return $actionFields[$action] ?? 'like_count';
    }

    public function id2postType($id): string
    {
        $postTypes = [
            0 => 'forum',
            1 => 'feed',
            2 => 'poll',
        ];
        return $postTypes[$id] ?? 'forum';
    }

    public function postType2id(string $type): int
    {
        $postTypes = [
            'forum' => 0,
            'feed' => 1,
            'poll' => 2,
        ];
        return $postTypes[$type] ?? 0;
    }

    /**
     * 36进制随机数
     * @param int $byteLength
     * @param string $prefix
     * @param int $toBase
     * @return string
     * @throws Exception
     */
    public function makeSlug(int $byteLength = 4, string $prefix = '', int $toBase = 36): string
    {
        // 转化成 36 进制，减少唯一索引的大小 base_convert 转化任何进制，最长8位
        return $prefix . base_convert(bin2hex(random_bytes($byteLength)), 16, $toBase);
    }

    /**
     * 36进制随机数
     * @param int $byteLength
     * @param string $prefix
     * @param int $toBase
     * @return string
     * @throws Exception
     */
    public static function makeSlug1(int $byteLength = 4, string $prefix = '', int $toBase = 36): string
    {
        // 转化成 36 进制，减少唯一索引的大小 base_convert 转化任何进制，最长8位
        return $prefix . base_convert(bin2hex(random_bytes($byteLength)), 16, $toBase);
    }

    /**
     * 10进制转换成其它任何进制的函数
     * @param int $num
     * @param int $to
     * @return string
     */
    public function decTo(int $num, int $to = 62): string
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        do {
            $result = $dict[bcmod($num, $to)] . $result;
            $num = bcdiv($num, $to);
        } while ($num > 0);
        return $result;
    }

    /**
     * 其它任何进制转换成10进制的函数
     * @param string $num
     * @param int $from
     * @return int|string
     */
    public function decFrom(string $num, int $from = 62): int|string
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($num);
        $dec = 0;
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $num[$i]);
            if ($pos >= $from) {
                continue; // 如果出现非法字符，会忽略掉。比如16进制中出现w、x、y、z等
            }
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }
        return $dec;
    }

    /**
     * @param array $value
     * @return string
     */
    public function encodePgArray(array $value): string
    {
        return str_replace(['[', ']'], ['{', '}'], json_encode($value));
    }

    /**
     * @param string $value
     * @return array
     */
    public function decodePgArray(string $value): array
    {
        return json_decode(str_replace(['{', '}'], ['[', ']'], $value));
    }

    /**
     * 一维数据数组生成数据树
     * @param array $list 数据列表
     * @param string $id ID Key
     * @param string $parentId 父ID Key
     * @param string $children 定义子数据Key
     * @return array
     */
    protected function arr2tree(array $list, string $id = 'id', string $parentId = 'parent_id', string $children = 'children'): array
    {
        [$tree, $map] = [[], []];
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item) {
            if (isset($item[$parentId], $map[$item[$parentId]])) {
                $map[$item[$parentId]][$children][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        return $tree;
    }


    protected function getWebsite(string $url): string
    {
        $urlInfo = parse_url(trim($url));
        return $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['path']) ? $urlInfo['path'] : '/');
    }
}
