<?php

namespace Layer\Base\Controllers;

use Closure;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Layer\Base\Traits\ResponseTrait;
use Layer\Base\Traits\UtilTrait;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BaseController extends Controller
{
    use UtilTrait;
    use ResponseTrait;

    protected Request $request;

    protected $pass;

    protected $userId;

    protected array $role;

    protected int $time;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->userId = $request->attributes->get('pass_id');
        // 客户端类型
        $this->time = time();
    }

    public function input($key = null, $default = null, $type = 'input')
    {
        if ($type === 'input') {
            return $this->request->input($key, $default);
        } else {
            return $this->request->route($key, $default);
        }
    }

    public function validate(array $rules, array $messages = [], array $customAttributes = [])
    {
        if (app()->runningInConsole()) {
            // @todo 把 rules 信息返回去
        } else {
            return app(Factory::class)->make(
                $this->request->all(), $rules, $messages, $customAttributes
            )->validate();
        }
    }

    /**
     * Create a new streamed response instance.
     *
     * @param Closure $callback
     * @param int $status
     * @param array $headers
     * @return StreamedResponse
     */
    public function stream(Closure $callback, int $status = 200, array $headers = []): StreamedResponse
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    /**
     * Create a new file download response.
     *
     * @param SplFileInfo|string $file
     * @param string $name
     * @param array $headers
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public function download(SplFileInfo|string $file, string $name = '', array $headers = [], string $disposition = 'attachment'): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);
        if (!$name) {
            return $response->setContentDisposition($disposition, $name, str_replace('%', '', Str::ascii($name)));
        }
        return $response;
    }
}
