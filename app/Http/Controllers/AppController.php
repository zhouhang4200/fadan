<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AesController;

class AppController extends Controller
{
    /**
     * 接口地址
     * @var array
     */
    private $api = [
        // 'list' => 'http://api.qsios.com/api/v1/game/list',
        // 'version' => 'http://api.qsios.com/api/v1/game/version',
        // 'server' => 'http://api.qsios.com/api/v1/game/server',
        // 'goods' => 'http://api.qsios.com/api/v1/game/goods',
        // 'order' => 'http://api.qsios.com/api/v1/order/game',

        'list' => 'http://api.qsios.com/api/v1/game/list',
        'version' => 'http://api.qsios.com/api/v1/game/version',
        'server' => 'http://api.qsios.com/api/v1/game/server',
        'goods' => 'http://api.qsios.com/api/v1/game/goods',
        'order' => 'http://api.qsios.com/api/v1/order/game',
        'buy' => 'http://api.qsios.com/api/v1/order/gift-card/buy',
    ];

    /**
     * app_id 请更换您自己的真实 app_id
     * @var string
     */
    private $appId = 'gri06wis39kcjvfjcz9qlykqsjybz9tjskgg';

    /**
     * app_secret 请更换您自己的真实 app_secret
     * @var string
     */
    private $appSecret = 'buisdhzmeelm5ubtssnx64vzwseux73ktfdvtejgnudfvu';

    /**
     * public_key 请更换您自己的真实 public_key
     * @var string
     */
    private $rsaPublicKey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDPgOPjNlaSphL3+JbhikJFxleVsjuba0xpegtRlWLnsBC6FNSP7Fz363vSedMsaQ6FA/FkCKl4xtnkGAusGhKkvNfKNCQZ1Ajq1njLhvxzJhAyDp+efseTKG1UeerNofB8iqPfiBq8jVQp5+APKjz1vxv6//nwHfVxmrLjCx+QLQIDAQAB
-----END PUBLIC KEY-----';

    /**
     * @param $api
     * @param $data
     * @return mixed
     */
    public function run($api, $data)
    {
        $requestData = array_merge($data, [
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'timestamp' => time(),
        ]);

        // 生成一个24位随机加密字符
        $aesKey = $this->randAesKey();

        // Aes加密数据
        $encryptData = (new AesController($aesKey))->encrypt(json_encode($requestData));
        // RSA加密Aes key
        // $encryptAesKey = openssl_public_encrypt($aesKey, $encrypted, $this->rsaPublicKey) ? bin2hex($encrypted) : null;
        $encryptAesKey = openssl_public_encrypt($aesKey, $encrypted, $this->rsaPublicKey) ? bin2hex($encrypted) : null;


        // 发送请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api[$api]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'app_id' => $this->appId,
            'data' => $encryptData,
            'key' => $encryptAesKey]);
        $output = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($output);

        // 如果有 content 则说明的返回数据
        if (isset($responseData->content)) {
            // 得到最终响应数据
            return $this->decrypt($responseData->content->key, $responseData->content->data);
        } else {
            return $responseData->message;
        }
    }

    /**
     * @param $key
     * @param $data
     * @return bool|string
     */
    public function decrypt($key, $data)
    {
        // 用RSA公钥解开key包得到 AesController 解密key
        $key = (openssl_public_decrypt(pack("H*", $key), $decrypted, $this->rsaPublicKey)) ? $decrypted : null;
        // 解data包
        return (new AesController($key))->decrypt($data);
    }

    /**
     * 随机生成 24 位 AesController key
     * @return string
     */
    private function randAesKey()
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i=0; $i<24; $i++)
        {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}
