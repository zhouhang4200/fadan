<?php
namespace App\Services;

use App\Http\Controllers\Frontend\Steam\Custom\Helper;
use App\Http\Controllers\Frontend\Steam\Custom\Mcrypt3Des;
use GuzzleHttp\Client;
use Exception;

/**
 * HTTP传输基类
 * @package App\Publics
 */
class HttpService
{
    const ENCODE_KEY = 'sdfjjjaJ';
    const ENCODE_IV  = 'sdfjjjaJ';

    /**
     * 请求接口
     * @param  $data string 数据
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request($data, $api, $jsonDecode = true)
    {
        $client = new Client();
        $res = $client->request('POST', $api, [
            'form_params' => [
                'data' => base64_encode((new Mcrypt3Des(self::ENCODE_KEY, self::ENCODE_IV))->encrypt($data)),
            ]
        ]);

        $content = $res->getBody()->getContents();

        // 发送日志
        Helper::log('http-request', ['请求接口', $api, $content]);

        if ($jsonDecode) {
            $content = json_decode($content, true);
        }

        return $content;
    }

    // 解密数据
    public function decode($value, $jsonDecode = true)
    {
        if (empty($value) || $value == 'null') {
            throw new Exception('返回数据为为空');
        }
        $res = (new Mcrypt3Des(self::ENCODE_KEY, self::ENCODE_IV))->decrypt(base64_decode($value));

        if ($jsonDecode) {
            $res = json_decode($res, true);
        }

        Helper::log('http-response', ['解密后参数：', $res]);
        return $res;
    }


    public function httpGet($api, $jsonDecode = true)
    {
        $client = new Client();
        $res = $client->request('GET', $api);

        $content = $res->getBody()->getContents();

        // 发送日志
        Helper::log('http-request', ['请求接口', $api, $content]);

        if ($jsonDecode) {
            $content = json_decode($content, true);
        }

        return $content;
    }
}
