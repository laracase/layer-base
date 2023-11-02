<?php

namespace Layer\Base\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * @param array|object $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendSuccessJson(array|object $data = [], string $message = '', int $code = 0): JsonResponse
    {
        if ($this->isEncrypt()) {
            $data = $this->encryptor($data);
        }
        $result = [
            'data' => $data,
            'code' => $code,
            'message' => $message,
            'time' => $this->time,
        ];
        return new JsonResponse($result);
    }


    /**
     * 判断是否需要加密返回数据
     * 默认都需要加密，通过 header 排除管理后台的接口
     * @return bool
     */
    private function isEncrypt(): bool
    {
        return false;
        // @todo 默认不加密
//        if (request()->header('X-Authenticated-App-Not-Encrypt')) {
//            return false;
//        }
//        return true;
    }

    private function encryptor($data): string
    {
        // 默认必须加密，加密算法
        $salt = md5(config('app.key'));
        $key = substr($salt, 6, 16);
        $iv = substr($salt, 16, 16);
        $data = json_encode($data);
        $data = openssl_encrypt($data, 'AES-128-CBC', $key, 0, $iv);
        return base64_encode($data);
    }

    /**
     * 返回错误信息
     * @param int $status
     * @param string $message
     * @param int $code
     * @param array $data
     * @return JsonResponse
     */
    public function sendFailedJson(int $status = 500, string $message = '系统错误', int $code = 1, array $data = []): JsonResponse
    {
        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'time' => time(),
        ];
        return new JsonResponse($result, $status);
    }

    /**
     * @param string $url
     * @param bool $permanent
     * @return void
     */
    public function redirect(string $url = '', bool $permanent = false): void
    {
        $url = $url ?: config('app.url');
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }
}
