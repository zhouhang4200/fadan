<?php
namespace App\Services;

use GuzzleHttp\Client;

/**
 * 短信发送接口
 * Class SmSApi
 * @package App\Publics
 */
class SmSApi
{
    /**
     * @var string
     */
    protected $host = 'http://fulu.10690007.net';

    /**
     * 短信发送
     * @param int $type 1 验信验证码 2 营销短信
     * @param int $to 发送给谁(手机号)
     * @param string $content 发送内容
     * @param int $userId 商户ID
     * @return bool
     */
    public function send($type, $to, $content)
    {
        $user = $type == 1 ? '20156' : '20157';
        $password = $type == 1 ? env('SMS_CODE_PASSWORD') : env('SMS_SALE_PASSWORD');

        //预定义参数，参数说明见文档
        $spSc = "02"; // 服务代码
        $sa = "10";  // 源地址
        $dc = "15";

        //拼接URI
        $request = "/sms/mt";
        $request .= "?command=MT_REQUEST&spid=" . $user . "&spsc=" . $spSc . "&sppassword=" . $password;
        $request .= "&sa=" . $sa . "&da=86" . $to . "&dc=" . $dc . "&sm=";
        $request .= bin2hex(mb_convert_encoding($content, "GBK", "UTF-8"));//下发内容转换HEX编码
        $uri = $this->host . $request;

        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', $uri);
        return  $result->getBody()->getContents();
    }

    // 发模板短息
    public static function sendTemplate($tel, $gameName = '')
    {
        $url = 'http://10.6.6.185:8209/api/Sms/SendMsg';
        $data = [
            'type'     => 10,
            'tel'      => $tel,
            'erpname'  => 'ios代充',
            'gamename' => $gameName,
        ];

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['form_params' => $data]);

        $contents = $res->getBody()->getContents();
        $arr = json_decode($contents);
        if ($arr->Code != '1000') {
            return false;
        }

        return true;
    }
}
